<?php
require_once 'db.php';

function fetchMessages($sender_id, $receiver_id) {
    global $pdo;
    $stmt = $pdo->prepare(
        "SELECT * FROM messages 
         WHERE (sender_id = ? AND receiver_id = ?) 
            OR (sender_id = ? AND receiver_id = ?) 
         ORDER BY created_at ASC"
    );
    $stmt->execute([$sender_id, $receiver_id, $receiver_id, $sender_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function sendMessage($sender_id, $receiver_id, $message) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->execute([$sender_id, $receiver_id, $message]);
    return $pdo->lastInsertId();
}
?>
