<?php
require_once 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $sender_id = $_SESSION['user_id'];
    $receiver_id = $data['receiver_id'];
    $message = $data['message'];

    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message, status) VALUES (?, ?, ?, 'sent')");
    $stmt->execute([$sender_id, $receiver_id, $message]);

    echo json_encode(['success' => true]);
}
?>
