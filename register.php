<?php
session_start();
require_once 'config/database.php';

$page_password = 'flyer2023';
$is_authorized = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['page_password'])) {
        if ($_POST['page_password'] === $page_password) {
            $is_authorized = true;
        } else {
            $error = "Incorrect page password";
        }
    } elseif ($is_authorized || isset($_SESSION['register_authorized'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        // Check if username already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Username already exists";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $hashed_password, $role])) {
                $_SESSION['register_success'] = true;
                header("Location: login.php");
                exit;
            } else {
                $error = "Registration failed";
            }
        }
    }
}

if ($is_authorized) {
    $_SESSION['register_authorized'] = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Flyer Development System</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <?php if (!$is_authorized && !isset($_SESSION['register_authorized'])): ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="page_password">Page Password:</label>
                    <input type="password" id="page_password" name="page_password" required>
                </div>
                <button type="submit">Submit</button>
            </form>
        <?php else: ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="role">Role:</label>
                    <select id="role" name="role" required>
                        <option value="management">Management</option>
                        <option value="designer">Designer</option>
                        <option value="proofreader">Proofreader</option>
                    </select>
                </div>
                <button type="submit">Register</button>
            </form>
        <?php endif; ?>
        
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>