<?php
require_once 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture']) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $file = $_FILES['profile_picture'];
    $upload_dir = 'uploads/';
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

    if (!in_array($file['type'], $allowed_types)) {
        echo "Invalid file type. Only JPG, PNG, and GIF are allowed.";
        exit();
    }

    $file_name = $upload_dir . uniqid() . "_" . basename($file['name']);
    if (move_uploaded_file($file['tmp_name'], $file_name)) {
        $stmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
        $stmt->execute([$file_name, $user_id]);
        echo "Profile picture uploaded successfully.";
    } else {
        echo "Failed to upload file.";
    }
}
?>
