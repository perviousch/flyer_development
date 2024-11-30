<?php
session_start();
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'proof_reader') {
    header("Location: login.php");
    exit;
}

// Fetch projects that need review
$stmt = $pdo->prepare("
    SELECT p.*, 
           (SELECT COUNT(*) FROM project_checks pc 
            WHERE pc.project_id = p.id 
            AND pc.user_id = :user_id 
            AND pc.draft_number = p.current_draft) as checked
    FROM projects p 
    WHERE p.status IN ('design_pending', 'draft_review', '') 
    ORDER BY p.created_at DESC
");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proofreader Dashboard - Flyer Development System</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Proofreader Dashboard</h1>
        <nav>
            <ul>
                <li><a href="#projects">Projects for Review</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
        <section id="projects">
            <h2>Projects for Review</h2>
            <?php if (empty($projects)): ?>
                <p>No projects currently available for review.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Current Draft</th>
                            <th>Created At</th>
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
                                <td><?php echo htmlspecialchars($project['current_draft']); ?></td>
                                <td><?php echo htmlspecialchars($project['created_at']); ?></td>
                                <td><?php echo $project['checked'] ? 'Yes' : 'No'; ?></td>
                                <td>
                                    <a href="project_review.php?id=<?php echo $project['id']; ?>" class="btn">Review</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>