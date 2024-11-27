<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'management') {
    header("Location: ../../login.php");
    exit;
}

$project_id = $_GET['id'] ?? null;

if (!$project_id) {
    header("Location: management_dashboard.php");
    exit;
}

// Fetch project details
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch();

if (!$project) {
    header("Location: management_dashboard.php");
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

// ... (rest of the existing PHP code remains unchanged)

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Details - Flyer Development System</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <style>
        /* ... (existing styles remain unchanged) ... */

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
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
    </style>
</head>
<body>
    <div class="container">
        <!-- ... (existing content remains unchanged) ... -->

        <div class="main-content">
            <h1>Project Details: <?php echo htmlspecialchars($project['name']); ?></h1>
            <nav>
                <ul>
                    <li><a href="../../management_dashboard.php">Back to Dashboard</a></li>
                    <li><a href="../../logout.php">Logout</a></li>
                </ul>
            </nav>

            <!-- ... (other sections remain unchanged) ... -->

            <section id="project-actions">
                <h2>Project Actions</h2>
                <button id="deleteProjectBtn" class="btn btn-danger">Delete Project</button>
            </section>

            <!-- ... (rest of the existing HTML remains unchanged) ... -->

        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Confirm Deletion</h2>
            <p>Are you sure you want to delete <strong><?php echo htmlspecialchars($project['name']); ?></strong>? It will be deleted permanently for all users.</p>
            <div class="modal-buttons">
                <form method="POST">
                    <button type="button" id="cancelDelete" class="btn">Cancel</button>
                    <button type="submit" name="confirm_delete_project" class="btn btn-danger">Confirm Delete</button>
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