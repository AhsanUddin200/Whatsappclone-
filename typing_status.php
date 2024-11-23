<?php
if (isset($_GET['receiver_id'])) {
    $receiver_id = $_GET['receiver_id'];
    $statusFile = "typing_{$receiver_id}.txt";

    if (file_exists($statusFile)) {
        echo file_get_contents($statusFile); // Return 'typing' or 'not_typing'
    } else {
        echo 'not_typing';
    }
}
?>
