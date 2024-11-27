<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'management') {
    exit('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_id'])) {
    $message_id = $_POST['message_id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO viewed_messages (user_id, message_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $message_id]);

    echo 'Success';
} else {
    echo 'Invalid request';
}