<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'designer') {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM projects WHERE status IN ('design_pending', 'draft_review') ORDER BY created_at DESC");
$projects = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Designer Dashboard - Flyer Development System</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Designer Dashboard</h1>
        <nav>
            <ul>
                <li><a href="#projects">Projects</a></li>
                <li><a href="#upload">Upload Design</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
        <section id="projects">
            <h2>Active Projects</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td><?php echo $project['id']; ?></td>
                            <td><?php echo $project['name']; ?></td>
                            <td><?php echo $project['status']; ?></td>
                            <td><?php echo $project['created_at']; ?></td>
                            <td>
                                <a href="view_project.php?id=<?php echo $project['id']; ?>">View</a>
                                <a href="upload_design.php?id=<?php echo $project['id']; ?>">Upload Design</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>