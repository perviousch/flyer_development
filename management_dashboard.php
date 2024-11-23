<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'management') {
    header("Location: login.php");
    exit;
}

// Fetch projects
$stmt = $pdo->query("SELECT p.*, 
    (SELECT COUNT(*) FROM project_checks pc WHERE pc.project_id = p.id) as check_count,
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Dashboard - Flyer Development System</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Management Dashboard</h1>
        <nav>
            <ul>
                <li><a href="#projects">Projects</a></li>
                <li><a href="#new-project">New Project</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>

        <section id="notifications">
            <h2>Notifications</h2>
            <?php if (count($awaiting_check) > 0): ?>
                <div class="notification warning">
                    <p>You have <?php echo count($awaiting_check); ?> project(s) awaiting your final review:</p>
                    <ul>
                        <?php foreach ($awaiting_check as $project): ?>
                            <li><a href="views/project/project.php?id=<?php echo $project['id']; ?>"><?php echo htmlspecialchars($project['name']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php else: ?>
                <p>No projects are currently awaiting your review.</p>
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
        // Add any necessary JavaScript here
        document.addEventListener('DOMContentLoaded', function() {
            // You can add interactive features here if needed
        });
    </script>
</body>
</html>