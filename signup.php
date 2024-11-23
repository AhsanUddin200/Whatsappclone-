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
    </style>
</head>
<body>
    <div class="container">
        <div class="signup-box">
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
