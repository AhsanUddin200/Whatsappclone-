<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $profile_picture = '';

    // Handle profile picture
    if (!empty($_POST['profile_picture_url'])) {
        $profile_picture = $_POST['profile_picture_url'];
    } elseif (!empty($_FILES['profile_picture_file']['tmp_name'])) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_name = $upload_dir . uniqid() . '_' . basename($_FILES['profile_picture_file']['name']);
        if (move_uploaded_file($_FILES['profile_picture_file']['tmp_name'], $file_name)) {
            $profile_picture = $file_name;
        }
    }

    // Generate the unique number
    $unique_number = generateUniqueNumber();

    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, unique_number, profile_picture) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $password, $unique_number, $profile_picture]);
        header("Location: login.php?success=1");
        exit();
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #075E54;
            font-family: Arial, sans-serif;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .signup-box {
            background-color: #fff;
            padding: 20px 40px;
            border-radius: 10px;
            text-align: center;
        }
        .signup-box form input, .signup-box form button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .signup-box form button {
            background-color: #25D366;
            color: white;
            border: none;
            cursor: pointer;
        }
        .signup-box img {
            max-width: 150px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="signup-box">
            <!-- Logo added here -->
            <img src="data:image/jpeg;base64,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" alt="Logo">
            <h2>Sign Up</h2>
            <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="name" placeholder="Enter your name" required>
                <input type="email" name="email" placeholder="Enter your email" required>
                <input type="password" name="password" placeholder="Enter your password" required>
                <input type="url" name="profile_picture_url" placeholder="Profile picture URL (optional)">
                <input type="file" name="profile_picture_file" accept="image/*">
                <button type="submit">Sign Up</button>
            </form>
        </div>
    </div>
</body>
</html>
