<?php 
session_start();
include '../config/db.php'; 
include '../includes/navbar.php'; // Include the navigation menu

$message = ''; // Initialize a message variable to display alerts

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Hash the password
    $name = trim($_POST['name']);
    $profile_photo = null;

    // Check if email already exists
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM Users WHERE email = ?");
    $stmtCheck->execute([$email]);
    if ($stmtCheck->fetchColumn() > 0) {
        $message = "<div class='alert alert-danger'>This email is already registered. Please use a different email.</div>";
    } else {
        // Handle file upload
        if (!empty($_FILES["profile_photo"]["name"])) {
            $target_dir = '../uploads/'; 
            $profile_photo = $target_dir . basename($_FILES["profile_photo"]["name"]);
            $imageFileType = strtolower(pathinfo($profile_photo, PATHINFO_EXTENSION));

            if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                if (!move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $profile_photo)) {
                    $message = "<div class='alert alert-warning'>Error uploading profile photo.</div>";
                    $profile_photo = null; // Reset profile_photo on failure
                }
            } else {
                $message = "<div class='alert alert-danger'>Invalid file type for profile photo.</div>";
                $profile_photo = null;
            }
        }

        // Insert into Users table
        $stmt = $pdo->prepare("INSERT INTO Users (email, password_hash, name, profile_photo) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$email, $password, $name, $profile_photo])) {
            $userId = $pdo->lastInsertId(); // Get the inserted user ID
            $roleId = 2; // Default role ID

            // Insert into User_Role table
            $stmtRole = $pdo->prepare("INSERT INTO User_Role (user_id, role_id) VALUES (?, ?)");
if ($stmtRole->execute([$userId, $roleId])) {
    echo "<div class='alert alert-success'>User  registered successfully.</div>";
} else {
    echo "<div class='alert alert-danger'>Error assigning user role.</div>";
}
        } else {
            $message = "<div class='alert alert-danger'>Error registering user.</div>";
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
    <!-- Display any messages -->
    <?php if (!empty($message)) echo $message; ?>
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
