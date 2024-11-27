<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'management') {
    header("Location: login.php");
    exit;
}

// Fetch projects
$stmt = $pdo->query("SELECT p.*, 
    (SELECT COUNT(*) FROM project_checks pc WHERE pc.project_id = p.id AND pc.draft_number = p.current_draft) as check_count,
    (SELECT COUNT(*) FROM users WHERE role IN ('designer', 'proof_reader')) as total_users
    FROM projects p ORDER BY p.created_at DESC");
$projects = $stmt->fetchAll();

// Handle new project creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_project'])) {
    $project_name = $_POST['project_name'];
    $project_date = $_POST['project_date'];
    $comment = $_POST['comment'];

    $stmt = $pdo->prepare("INSERT INTO projects (name, status, project_date, comment) VALUES (?, 'design_pending', ?, ?)");
    $stmt->execute([$project_name, $project_date, $comment]);

    header("Location: management_dashboard.php");
    exit;
}

// Fetch projects awaiting manager's check
$stmt = $pdo->prepare("SELECT p.* FROM projects p
    LEFT JOIN project_checks pc ON p.id = pc.project_id AND pc.user_id = ?
    WHERE p.status = 'final_review' AND pc.id IS NULL");
$stmt->execute([$_SESSION['user_id']]);
$awaiting_check = $stmt->fetchAll();

// Fetch new messages
$stmt = $pdo->prepare("
    SELECT cm.*, p.name as project_name, p.id as project_id
    FROM chat_messages cm
    JOIN projects p ON cm.project_id = p.id
    LEFT JOIN viewed_messages vm ON cm.id = vm.message_id AND vm.user_id = ?
    WHERE vm.id IS NULL
    ORDER BY cm.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$new_messages = $stmt->fetchAll();

// Fetch new designer uploads
$stmt = $pdo->prepare("
    SELECT p.id, p.name, p.current_draft
    FROM projects p
    WHERE p.pdf_file_path IS NOT NULL
    AND p.updated_at > (SELECT COALESCE(MAX(last_viewed), '1970-01-01') FROM project_views WHERE user_id = ?)
    ORDER BY p.updated_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$new_uploads = $stmt->fetchAll();

// Fetch new proofreader checks
$stmt = $pdo->prepare("
    SELECT pc.*, p.name as project_name, u.username
    FROM project_checks pc
    JOIN projects p ON pc.project_id = p.id
    JOIN users u ON pc.user_id = u.id
    WHERE pc.check_date > (SELECT COALESCE(MAX(last_viewed), '1970-01-01') FROM check_views WHERE user_id = ?)
    AND u.role = 'proof_reader'
    ORDER BY pc.check_date DESC
");
$stmt->execute([$_SESSION['user_id']]);
$new_checks = $stmt->fetchAll();

// Update last viewed timestamps for uploads and checks
$pdo->prepare("INSERT INTO project_views (user_id, last_viewed) VALUES (?, NOW()) ON DUPLICATE KEY UPDATE last_viewed = NOW()")->execute([$_SESSION['user_id']]);
$pdo->prepare("INSERT INTO check_views (user_id, last_viewed) VALUES (?, NOW()) ON DUPLICATE KEY UPDATE last_viewed = NOW()")->execute([$_SESSION['user_id']]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Dashboard - Flyer Development System</title>
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        .notification {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .notification.warning { background-color: #fff3cd; color: #856404; }
        .notification.info { background-color: #d1ecf1; color: #0c5460; }
        .notification.message { background-color: #d4edda; color: #155724; }
        .notification.upload { background-color: #f8d7da; color: #721c24; }
        .notification.check { background-color: #e2e3e5; color: #383d41; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Management Dashboard</h1>
        <nav>
            <ul>
                <li><a href="#notifications">Notifications</a></li>
                <li><a href="#projects">Projects</a></li>
                <li><a href="#new-project">New Project</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>

        <section id="notifications">
            <h2>Notifications</h2>
            <?php if (count($awaiting_check) > 0): ?>
                <div class="notification warning" data-type="awaiting_check">
                    <p>You have <?php echo count($awaiting_check); ?> project(s) awaiting your final review:</p>
                    <ul>
                        <?php foreach ($awaiting_check as $project): ?>
                            <li><a href="views/project/project.php?id=<?php echo $project['id']; ?>"><?php echo htmlspecialchars($project['name']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php foreach ($new_messages as $message): ?>
                <div class="notification message" data-type="message" data-id="<?php echo $message['id']; ?>">
                    <p>New message in project "<a href="views/project/project.php?id=<?php echo $message['project_id']; ?>"><?php echo htmlspecialchars($message['project_name']); ?></a>"</p>
                </div>
            <?php endforeach; ?>

            <?php foreach ($new_uploads as $upload): ?>
                <div class="notification upload" data-type="upload" data-id="<?php echo $upload['id']; ?>">
                    <p>Designer has uploaded new draft (<?php echo $upload['current_draft']; ?>) for "<a href="views/project/project.php?id=<?php echo $upload['id']; ?>"><?php echo htmlspecialchars($upload['name']); ?></a>"</p>
                </div>
            <?php endforeach; ?>

            <?php foreach ($new_checks as $check): ?>
                <div class="notification check" data-type="check" data-id="<?php echo $check['id']; ?>">
                    <p>Proofreader <?php echo htmlspecialchars($check['username']); ?> has checked draft <?php echo $check['draft_number']; ?> in project "<a href="views/project/project.php?id=<?php echo $check['project_id']; ?>"><?php echo htmlspecialchars($check['project_name']); ?></a>"</p>
                </div>
            <?php endforeach; ?>

            <?php if (count($awaiting_check) == 0 && count($new_messages) == 0 && count($new_uploads) == 0 && count($new_checks) == 0): ?>
                <p>No new notifications.</p>
            <?php endif; ?>
        </section>

        <section id="new-project">
            <h2>Create New Project</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="project_name">Project Name:</label>
                    <input type="text" id="project_name" name="project_name" required>
                </div>
                <div class="form-group">
                    <label for="project_date">Project Date:</label>
                    <input type="date" id="project_date" name="project_date" required>
                </div>
                <div class="form-group">
                    <label for="comment">Comment:</label>
                    <textarea id="comment" name="comment" rows="3"></textarea>
                </div>
                <button type="submit" name="create_project">Create Project</button>
            </form>
        </section>

        <section id="projects">
            <h2>Projects</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Checks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td><?php echo $project['id']; ?></td>
                            <td><?php echo htmlspecialchars($project['name']); ?></td>
                            <td><?php echo $project['project_date']; ?></td>
                            <td><?php echo $project['status']; ?></td>
                            <td><?php echo $project['check_count']; ?> / <?php echo $project['total_users']; ?></td>
                            <td>
                                <a href="views/project/project.php?id=<?php echo $project['id']; ?>" class="btn">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notifications = document.querySelectorAll('.notification');
            notifications.forEach(notification => {
                notification.addEventListener('click', function() {
                    const type = this.dataset.type;
                    const id = this.dataset.id;
                    if (type === 'message') {
                        fetch('mark_message_read.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'message_id=' + id
                        }).then(() => {
                            this.style.display = 'none';
                        });
                    } else {
                        this.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>