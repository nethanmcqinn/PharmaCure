<?php 
session_start(); // Start the session 
include '../config/db.php'; // Include your database connection 
include '../includes/navbar.php'; // Include the navigation menu 

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Hash the password
    $name = trim($_POST['name']);

    // Check if email already exists
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmtCheck->execute([$email]);
    $emailExists = $stmtCheck->fetchColumn();

    if ($emailExists) {
        echo "<div class='alert alert-danger'>This email is already registered. Please use a different email.</div>";
    } else {
        // Handle file upload if a profile photo is provided
        $profile_photo = null;
        if (!empty($_FILES["profile_photo"]["name"])) {
            $target_dir = 'C:\xampp\htdocs\PharmaCure\PharmaCure2\public\uploads'; // Ensure this directory exists and is writable
            $profile_photo = $target_dir . basename($_FILES["profile_photo"]["name"]);
            
            // Check if the directory is writable
            if (is_writable('C:\xampp\htdocs\PharmaCure\PharmaCure2\public\uploads')) {
                if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $profile_photo)) {
                    // File uploaded successfully
                } else {
                    echo "<div class='alert alert-warning'>Error uploading profile photo.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Upload directory is not writable.</div>";
            }
        }

        // Insert into users table
        $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, name, profile_photo) VALUES (?, ?, ?, ?)");
        
        if ($stmt->execute([$email, $password, $name, $profile_photo])) {
            echo "<div class='alert alert-success'>User registered successfully.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error registering user.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h1>User Registration</h1>
    <form action="user_register.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="name">Full Name:</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="profile_photo">Profile Photo:</label>
            <input type="file" name="profile_photo" class="form-control-file">
        </div>

        <button type="submit" class="btn btn-primary">Register</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="../cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="../stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>