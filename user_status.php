<?php
require_once 'db.php';

function updateUserStatus($user_id, $is_online) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO user_status (user_id, is_online, last_seen) 
                           VALUES (?, ?, NOW())
                           ON DUPLICATE KEY UPDATE is_online = ?, last_seen = NOW()");
    $stmt->execute([$user_id, $is_online, $is_online]);
}

function fetchUserStatus($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT is_online, last_seen FROM user_status WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
