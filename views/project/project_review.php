<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'designer') {
    header("Location: ../../login.php");
    exit;
}

$project_id = $_GET['id'] ?? null;

if (!$project_id) {
    header("Location: ../../designer_dashboard.php");
    exit;
}

// Fetch project details
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch();

if (!$project) {
    header("Location: ../../designer_dashboard.php");
    exit;
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

// Handle designer check submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['designer_check'])) {
    $check_status = $_POST['designer_check'] === 'approve' ? 'checked' : 'changes_required';
    $stmt = $pdo->prepare("INSERT INTO project_checks (project_id, user_id, status, draft_number, check_date) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$project_id, $_SESSION['user_id'], $check_status, $project['current_draft']]);
    
    // Refresh project checks
    $stmt = $pdo->prepare("SELECT pc.*, u.username 
                           FROM project_checks pc 
                           JOIN users u ON pc.user_id = u.id 
                           WHERE pc.project_id = ? AND pc.draft_number = ?
                           ORDER BY pc.check_date DESC");
    $stmt->execute([$project_id, $project['current_draft']]);
    $project_checks = $stmt->fetchAll();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Review - Flyer Development System</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Add the same styles as in project.php */
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
            <?php if ($project['status'] !== 'approved_for_print'): ?>
            <form method="POST">
                <div class="form-group">
                    <label for="chat_message">New Message:</label>
                    <textarea id="chat_message" name="chat_message" required></textarea>
                </div>
                <button type="submit"><i class="fas fa-paper-plane"></i> Send Message</button>
            </form>
            <?php else: ?>
            <p>This project has been approved for print. The chat is now closed.</p>
            <?php endif; ?>
        </div>
        <div class="main-content">
            <h1><i class="fas fa-project-diagram"></i> Project Review: <?php echo htmlspecialchars($project['name']); ?></h1>
            <nav>
                <ul>
                    <li><a href="../../designer_dashboard.php"><i class="fas fa-tachometer-alt"></i> Back to Dashboard</a></li>
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

            <?php if ($project['status'] !== 'approved_for_print'): ?>
            <section id="designer-check">
                <h2><i class="fas fa-check-circle"></i> Designer Check</h2>
                <form method="POST">
                    <button type="submit" name="designer_check" value="approve" class="btn btn-success"><i class="fas fa-thumbs-up"></i> Approve</button>
                    <button type="submit" name="designer_check" value="reject" class="btn btn-danger"><i class="fas fa-thumbs-down"></i> Request Changes</button>
                </form>
            </section>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

