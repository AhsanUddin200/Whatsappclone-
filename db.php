<?php
// Database connection parameters
$host = "localhost";
$dbname = "whatsapp";
$username = "root";
$password = "";

try {
    // Establish database connection using PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to generate a unique number
function generateUniqueNumber() {
    global $pdo;

    // Get the highest numeric part of the unique_number from the database
    $stmt = $pdo->query("SELECT MAX(CAST(SUBSTRING_INDEX(unique_number, '-', -1) AS UNSIGNED)) AS last_num FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Start at 1000 if no users exist
    $lastNum = $result['last_num'] ?? 1000;

    // Increment the number to generate the next unique number
    $newNum = $lastNum + 1;

    // Format the unique number as +786-XXXX
    return "+786-" . str_pad($newNum, 4, '0', STR_PAD_LEFT);
}
?>
