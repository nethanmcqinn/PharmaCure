<?php 
session_start(); // Start the session
include '../config/db.php'; // Include your database connection

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the password is correct
    $adminPassword = $_POST['admin_password'];
    if ($adminPassword !== 'adminpassword') {
        $error = "Incorrect password. You are not authorized to register an admin.";
    } else {
        // If the password is correct, proceed with registration
        $email = $_POST['email'];
        $name = $_POST['name'];
        $password = $_POST['password'];
        
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert into the users table with role as admin
        $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, name, role, is_active, created_at, updated_at) VALUES (?, ?, ?, 'admin', 1, NOW(), NOW())");
        
        if ($stmt->execute([$email, $hashedPassword, $name])) {
            $success = "Admin registered successfully!";
        } else {
            $error = "Failed to register admin. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h1>Register Admin</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form action="admin_register.php" method="POST">
        <div class="form-group">
            <label for="admin_password">Admin Password:</label>
            <input type="password" name="admin_password" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Register Admin</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="../cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="../stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>