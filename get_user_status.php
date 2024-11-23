<?php
require_once 'db.php';

if (!isset($_GET['user_id'])) {
    echo json_encode(['error' => 'User ID not provided']);
    exit();
}

$user_id = $_GET['user_id'];

$stmt = $pdo->prepare("SELECT is_online, last_seen FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo json_encode([
        'is_online' => (bool) $user['is_online'],
        'last_seen' => $user['last_seen']
    ]);
} else {
    echo json_encode(['error' => 'User not found']);
}
?>
