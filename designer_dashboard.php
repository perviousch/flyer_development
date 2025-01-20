<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'designer') {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT p.*, 
    (SELECT COUNT(*) FROM project_checks pc WHERE pc.project_id = p.id AND pc.user_id = ? AND pc.checker_role = 'designer') as checked,
    (SELECT MAX(version_number) FROM file_versions fv WHERE fv.project_id = p.id) as latest_version
    FROM projects p 
    WHERE p.status IN ('design_pending', 'design_in_progress', 'review_pending', 'changes_requested') 
    ORDER BY p.updated_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$projects = $stmt->fetchAll();

// Fetch new messages
$stmt = $pdo->prepare("
    SELECT cm.*, p.name as project_name, p.id as project_id
    FROM chat_messages cm
    JOIN projects p ON cm.project_id = p.id
    LEFT JOIN viewed_messages vm ON cm.id = vm.message_id AND vm.user_id = ?
    WHERE vm.id IS NULL AND cm.is_closed = FALSE
    ORDER BY cm.created_at DESC
    LIMIT 5
");
$stmt->execute([$_SESSION['user_id']]);
$new_messages = $stmt->fetchAll();

// Fetch projects with recent updates
$stmt = $pdo->prepare("
    SELECT p.id, p.name, p.updated_at
    FROM projects p
    WHERE p.updated_at > (SELECT COALESCE(MAX(last_viewed), '1970-01-01') FROM project_views WHERE user_id = ?)
    AND p.status IN ('design_pending', 'design_in_progress', 'review_pending', 'changes_requested')
    ORDER BY p.updated_at DESC
    LIMIT 5
");
$stmt->execute([$_SESSION['user_id']]);
$updated_projects = $stmt->fetchAll();

// Update last viewed timestamp for projects
$pdo->prepare("INSERT INTO project_views (user_id, last_viewed) VALUES (?, NOW()) ON DUPLICATE KEY UPDATE last_viewed = NOW()")->execute([$_SESSION['user_id']]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Designer Dashboard - Flyer Development System</title>
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        .notification {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Designer Dashboard</h1>
        <nav>
            <ul>
                <li><a href="#notifications">Notifications</a></li>
                <li><a href="#projects">Projects</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>

        <section id="notifications">
            <h2>Notifications</h2>
            <?php if (count($new_messages) > 0): ?>
                <?php foreach ($new_messages as $message): ?>
                    <div class="notification" data-type="message" data-id="<?php echo $message['id']; ?>">
                        <p>New message in project "<a href="project_review.php?id=<?php echo $message['project_id']; ?>"><?php echo htmlspecialchars($message['project_name']); ?></a>"</p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (count($updated_projects) > 0): ?>
                <?php foreach ($updated_projects as $project): ?>
                    <div class="notification" data-type="project_update" data-id="<?php echo $project['id']; ?>">
                        <p>Project "<a href="project_review.php?id=<?php echo $project['id']; ?>"><?php echo htmlspecialchars($project['name']); ?></a>" has been updated</p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (count($new_messages) == 0 && count($updated_projects) == 0): ?>
                <p>No new notifications.</p>
            <?php endif; ?>
        </section>

        <section id="projects">
            <h2>Active Projects</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th>Latest Version</th>
                        <th>Checked</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($project['id']); ?></td>
                            <td><?php echo htmlspecialchars($project['name']); ?></td>
                            <td><?php echo htmlspecialchars($project['status']); ?></td>
                            <td><?php echo htmlspecialchars($project['updated_at']); ?></td>
                            <td><?php echo $project['latest_version'] ? htmlspecialchars($project['latest_version']) : 'N/A'; ?></td>
                            <td><?php echo $project['checked'] ? 'Yes' : 'No'; ?></td>
                            <td>
                                <a href="project_review.php?id=<?php echo $project['id']; ?>" class="btn">View Details</a>
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