<?php
session_start();
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'proof_reader') {
    header("Location: login.php");
    exit;
}

$project_id = $_GET['id'] ?? null;

if (!$project_id) {
    header("Location: proofreader_dashboard.php");
    exit;
}

// Fetch project details
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch();

if (!$project) {
    header("Location: proofreader_dashboard.php");
    exit;
}

// Handle proofreader check
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proofreader_check'])) {
    $status = $_POST['check_status'];
    $stmt = $pdo->prepare("INSERT INTO project_checks (project_id, user_id, status, draft_number) 
                           VALUES (?, ?, ?, ?) 
                           ON DUPLICATE KEY UPDATE status = ?");
    $stmt->execute([$project_id, $_SESSION['user_id'], $status, $project['current_draft'], $status]);
}

// Fetch chat messages
$stmt = $pdo->prepare("SELECT cm.*, u.username, u.role 
                       FROM chat_messages cm 
                       JOIN users u ON cm.user_id = u.id 
                       WHERE cm.project_id = ? 
                       ORDER BY cm.created_at DESC 
                       LIMIT 50");
$stmt->execute([$project_id]);
$chat_messages = $stmt->fetchAll();

// Handle new chat message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['chat_message'])) {
    $message = trim($_POST['chat_message']);
    if (!empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO chat_messages (project_id, user_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$project_id, $_SESSION['user_id'], $message]);
        // Refresh chat messages
        $stmt = $pdo->prepare("SELECT cm.*, u.username, u.role 
                               FROM chat_messages cm 
                               JOIN users u ON cm.user_id = u.id 
                               WHERE cm.project_id = ? 
                               ORDER BY cm.created_at DESC 
                               LIMIT 50");
        $stmt->execute([$project_id]);
        $chat_messages = $stmt->fetchAll();
    }
}

// Fetch product data
$stmt = $pdo->prepare("SELECT * FROM product_data WHERE project_id = ? ORDER BY page_number, product_name");
$stmt->execute([$project_id]);
$products = $stmt->fetchAll();

// Get unique categories and pages for filtering
$categories = array_unique(array_column($products, 'category_name'));
$pages = array_unique(array_column($products, 'page_number'));

// Apply filters
$category_filter = $_GET['category'] ?? '';
$page_filter = $_GET['page'] ?? '';

if ($category_filter || $page_filter) {
    $products = array_filter($products, function($product) use ($category_filter, $page_filter) {
        return (!$category_filter || $product['category_name'] == $category_filter) &&
               (!$page_filter || $product['page_number'] == $page_filter);
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Review - Flyer Development System</title>
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        .container {
            display: flex;
        }
        .chat-sidebar {
            width: 300px;
            padding: 20px;
            border-right: 1px solid #ccc;
        }
        .main-content {
            flex-grow: 1;
            padding: 20px;
        }
        #chat-container {
            height: 400px;
            overflow-y: auto;
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
        }
        .chat-message {
            margin-bottom: 10px;
        }
        .chat-message .username {
            font-weight: bold;
        }
        .chat-message .timestamp {
            font-size: 0.8em;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="chat-sidebar">
            <h2>Project Chat</h2>
            <div id="chat-container">
                <?php foreach ($chat_messages as $message): ?>
                    <div class="chat-message">
                        <span class="username"><?php echo htmlspecialchars($message['username']); ?> (<?php echo htmlspecialchars($message['role']); ?>):</span>
                        <span class="message"><?php echo htmlspecialchars($message['message']); ?></span>
                        <span class="timestamp"><?php echo htmlspecialchars($message['created_at']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <form method="POST">
                <div class="form-group">
                    <label for="chat_message">New Message:</label>
                    <textarea id="chat_message" name="chat_message" required></textarea>
                </div>
                <button type="submit">Send Message</button>
            </form>
        </div>
        <div class="main-content">
            <h1>Project Review: <?php echo htmlspecialchars($project['name']); ?></h1>
            <nav>
                <ul>
                    <li><a href="proofreader_dashboard.php">Back to Dashboard</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>

            <section id="project-info">
                <h2>Project Information</h2>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($project['status']); ?></p>
                <p><strong>Date:</strong> <?php echo htmlspecialchars($project['project_date']); ?></p>
                <p><strong>Last Update:</strong> <?php echo htmlspecialchars($project['updated_at']); ?></p>
                <p><strong>Current Draft:</strong> <?php echo htmlspecialchars($project['current_draft']); ?></p>
            </section>

            <section id="pdf-preview">
                <h2>PDF Preview</h2>
                <?php if ($project['pdf_file_path']): ?>
                    <embed src="<?php echo htmlspecialchars($project['pdf_file_path']); ?>" type="application/pdf" width="100%" height="600px" />
                <?php else: ?>
                    <p>No PDF uploaded yet.</p>
                <?php endif; ?>
            </section>

            <section id="product-list">
                <h2>Product List</h2>
                <form method="GET">
                    <input type="hidden" name="id" value="<?php echo $project_id; ?>">
                    <div class="form-group">
                        <label for="category">Filter by Category:</label>
                        <select name="category" id="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category); ?>" <?php echo $category_filter == $category ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="page">Filter by Page:</label>
                        <select name="page" id="page">
                            <option value="">All Pages</option>
                            <?php foreach ($pages as $page): ?>
                                <option value="<?php echo htmlspecialchars($page); ?>" <?php echo $page_filter == $page ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($page); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit">Apply Filters</button>
                </form>
                <table>
                    <thead>
                        <tr>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            <th>Category Name</th>
                            <th>Catalog Name</th>
                            <th>Bulk Price</th>
                            <th>Current Price</th>
                            <th>Promo Price</th>
                            <th>Page Number</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['product_code']); ?></td>
                                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                <td><?php echo htmlspecialchars($product['catalogue_name']); ?></td>
                                <td><?php echo htmlspecialchars($product['bulk_price']); ?></td>
                                <td><?php echo htmlspecialchars($product['current_sp']); ?></td>
                                <td><?php echo htmlspecialchars($product['promo_sp']); ?></td>
                                <td><?php echo htmlspecialchars($product['page_number']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <section id="proofreader-check">
                <h2>Proofreader Check</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="check_status">Draft Status:</label>
                        <select id="check_status" name="check_status" required>
                            <option value="checked">Correct</option>
                            <option value="rejected">Wrong</option>
                        </select>
                    </div>
                    <button type="submit" name="proofreader_check">Submit Check</button>
                </form>
            </section>
        </div>
    </div>
</body>
</html>