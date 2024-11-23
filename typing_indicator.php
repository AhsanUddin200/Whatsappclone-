<?php
require_once 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_POST['receiver_id'];
    $typing = $_POST['typing'] === 'true' ? 'typing' : 'not_typing';

    // Save typing status in a temporary file (or use a database for scalability)
    file_put_contents("typing_{$receiver_id}.txt", $typing);
    echo json_encode(['success' => true]);
}
?>
