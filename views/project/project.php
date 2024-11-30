<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'management') {
    header("Location: ../../login.php");
    exit;
}

$project_id = $_GET['id'] ?? null;

if (!$project_id) {
    header("Location: ../../management_dashboard.php");
    exit;
}

// Fetch project details
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch();

if (!$project) {
    header("Location: ../../management_dashboard.php");
    exit;
}

// Handle project deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete_project'])) {
    // Delete associated records
    $pdo->prepare("DELETE FROM product_data WHERE project_id = ?")->execute([$project_id]);
    $pdo->prepare("DELETE FROM chat_messages WHERE project_id = ?")->execute([$project_id]);
    $pdo->prepare("DELETE FROM project_checks WHERE project_id = ?")->execute([$project_id]);
    
    // Delete the project
    $pdo->prepare("DELETE FROM projects WHERE id = ?")->execute([$project_id]);
    
    // Redirect to dashboard
    header("Location: ../../management_dashboard.php");
    exit;
}

// Handle final approval
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['final_approval'])) {
    // Update project status
    $stmt = $pdo->prepare("UPDATE projects SET status = 'approved_for_print' WHERE id = ?");
    $stmt->execute([$project_id]);

    // Update all project checks to 'checked'
    $stmt = $pdo->prepare("UPDATE project_checks SET status = 'checked' WHERE project_id = ? AND draft_number = ?");
    $stmt->execute([$project_id, $project['current_draft']]);

    // Close the chat room for this project
    $stmt = $pdo->prepare("UPDATE chat_messages SET is_closed = 'yes' WHERE project_id = ?");
    $stmt->execute([$project_id]);

    // Refresh project details
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->execute([$project_id]);
    $project = $stmt->fetch();
}

// Handle CSV upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    if (($handle = fopen($_FILES['csv_file']['tmp_name'], "r")) !== FALSE) {
        $stmt = $pdo->prepare("INSERT INTO product_data (project_id, product_code, product_name, category_name, catalogue_name, bulk_price, current_sp, promo_sp, page_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        // Skip the header row
        fgetcsv($handle);
        
        // Prepare statement to check for existing product codes
        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM product_data WHERE project_id = ? AND product_code = ?");
        
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $product_code = trim($data[0]);
            
            // Skip if product code is empty
            if (empty($product_code)) {
                continue;
            }
            
            // Check if product code already exists
            $check_stmt->execute([$project_id, $product_code]);
            if ($check_stmt->fetchColumn() > 0) {
                continue; // Skip if product code is a duplicate
            }
            
            $stmt->execute([
                $project_id,
                $product_code,
                $data[1], // product_name
                $data[2], // category_name
                $data[3], // catalogue_name
                $data[4], // bulk_price (now VARCHAR)
                $data[5], // current_sp
                $data[6], // promo_sp
                $data[7]  // page_number (now VARCHAR)
            ]);
        }
        fclose($handle);
    }
}

// Fetch product data
$stmt = $pdo->prepare("SELECT * FROM product_data WHERE project_id = ? ORDER BY page_number, product_name");
$stmt->execute([$project_id]);
$products = $stmt->fetchAll();

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
        $stmt = $pdo->prepare("INSERT INTO chat_messages (project_id, user_id, message, is_closed) VALUES (?, ?, ?, 'no')");
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

// Fetch project checks
$stmt = $pdo->prepare("SELECT pc.*, u.username 
                       FROM project_checks pc 
                       JOIN users u ON pc.user_id = u.id 
                       WHERE pc.project_id = ? AND pc.draft_number = ?
                       ORDER BY pc.check_date DESC");
$stmt->execute([$project_id, $project['current_draft']]);
$project_checks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Details - Flyer Development System</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #121212;
            color: #e0e0e0;
        }
        .container {
            display: flex;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .chat-sidebar {
            width: 300px;
            padding: 20px;
            border-right: 1px solid #444;
            background-color: #1e1e1e;
            box-shadow: 2px 0 5px rgba(0,0,0,0.3);
        }
        .main-content {
            flex-grow: 1;
            padding: 20px;
        }
        #chat-container {
            height: 400px;
            overflow-y: auto;
            border: 1px solid #444;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #2a2a2a;
        }
        .chat-message {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
            background-color: #3a3a3a;
            color: #e0e0e0;
        }
        .chat-message .username {
            font-weight: bold;
            color: #4CAF50;
        }
        .chat-message .timestamp {
            font-size: 0.8em;
            color: #aaa;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.6);
        }
        .modal-content {
            background-color: #2a2a2a;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #444;
            width: 80%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
            border-radius: 5px;
            color: #e0e0e0;
        }
        .modal-content p {
            color: #e0e0e0;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: #fff;
            text-decoration: none;
            cursor: pointer;
        }
        .modal-buttons {
            text-align: right;
            margin-top: 20px;
        }
        .modal-buttons button {
            margin-left: 10px;
        }
        h1, h2 {
            color: #4CAF50;
            text-align: center;
        }
        nav ul {
            list-style-type: none;
            padding: 0;
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        nav ul li {
            margin: 0 10px;
        }
        nav ul li a {
            text-decoration: none;
            color: #4CAF50;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        nav ul li a:hover {
            background-color: rgba(76, 175, 80, 0.2);
        }
        section {
            background-color: #1e1e1e;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            transition: box-shadow 0.3s;
            border: 1px solid #444;
        }
        section:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.5);
        }
        button, .btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover, .btn:hover {
            background-color: #45a049;
        }
        .btn-danger {
            background-color: #f44336;
        }
        .btn-danger:hover {
            background-color: #d32f2f;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #444;
            color: #e0e0e0;
        }
        th {
            background-color: #2a2a2a;
            font-weight: bold;
            color: #4CAF50;
        }
        tr:hover {
            background-color: rgba(76, 175, 80, 0.1);
        }
        input[type="file"], textarea {
            background-color: #2a2a2a;
            color: #e0e0e0;
            border: 1px solid #444;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
        }
        .error {
            color: #f44336;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="chat-sidebar">
            <h2><i class="fas fa-comments"></i> Project Chat</h2>
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
                <button type="submit"><i class="fas fa-paper-plane"></i> Send Message</button>
            </form>
        </div>
        <div class="main-content">
            <h1><i class="fas fa-project-diagram"></i> Project Details: <?php echo htmlspecialchars($project['name']); ?></h1>
            <nav>
                <ul>
                    <li><a href="../../management_dashboard.php"><i class="fas fa-tachometer-alt"></i> Back to Dashboard</a></li>
                    <li><a href="../../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>

            <section id="project-info">
                <h2><i class="fas fa-info-circle"></i> Project Information</h2>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($project['status']); ?></p>
                <p><strong>Date:</strong> <?php echo htmlspecialchars($project['project_date']); ?></p>
                <p><strong>Last Update:</strong> <?php echo htmlspecialchars($project['updated_at']); ?></p>
                <p><strong>Comment:</strong> <?php echo htmlspecialchars($project['comment'] ?? 'No comment'); ?></p>
            </section>

            <section id="project-actions">
                <h2><i class="fas fa-cogs"></i> Project Actions</h2>
                <button id="deleteProjectBtn" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Delete Project</button>
                <?php if ($project['status'] != 'approved_for_print'): ?>
                    <form method="POST" style="display: inline;">
                        <button type="submit" name="final_approval" class="btn btn-success"><i class="fas fa-check-circle"></i> Final Approval</button>
                    </form>
                <?php endif; ?>
            </section>

            <section id="project-checks">
                <h2><i class="fas fa-tasks"></i> Project Checks</h2>
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Status</th>
                            <th>Check Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($project_checks as $check): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($check['username']); ?></td>
                                <td><?php echo htmlspecialchars($check['status']); ?></td>
                                <td><?php echo htmlspecialchars($check['check_date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <section id="pdf-preview">
                <h2><i class="fas fa-file-pdf"></i> PDF Preview</h2>
                <?php if ($project['pdf_file_path']): ?>
                    <embed src="<?php echo htmlspecialchars('../../' . $project['pdf_file_path']); ?>" type="application/pdf" width="100%" height="600px" />
                    <p><a href="<?php echo htmlspecialchars('../../' . $project['pdf_file_path']); ?>" download><i class="fas fa-download"></i> Download PDF</a></p>
                <?php else: ?>
                    <p>No PDF uploaded yet.</p>
                <?php endif; ?>
            </section>

            <section id="csv-upload">
                <h2><i class="fas fa-file-csv"></i> Upload Product Data</h2>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="csv_file">Upload CSV:</label>
                        <input type="file" id="csv_file" name="csv_file" accept=".csv" required>
                    </div>
                    <button type="submit"><i class="fas fa-upload"></i> Upload CSV</button>
                </form>
            </section>

            <section id="product-list">
                <h2><i class="fas fa-list"></i> Product List</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
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
                                <td><?php echo htmlspecialchars($product['id']); ?></td>
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
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h2>
            <p>Are you sure you want to delete <strong><?php echo htmlspecialchars($project['name']); ?></strong>? It will be deleted permanently for all users.</p>
            <div class="modal-buttons">
                <form method="POST">
                    <button type="button" id="cancelDelete" class="btn"><i class="fas fa-times"></i> Cancel</button>
                    <button type="submit" name="confirm_delete_project" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Confirm Delete</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Get the modal
        var modal = document.getElementById("deleteModal");

        // Get the button that opens the modal
        var btn = document.getElementById("deleteProjectBtn");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // Get the cancel button
        var cancelBtn = document.getElementById("cancelDelete");

        // When the user clicks the button, open the modal 
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks on cancel, close the modal
        cancelBtn.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>

