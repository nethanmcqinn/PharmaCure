<?php session_start(); ?>
<?php include 'PharmaCure2\config'; ?>
<?php include 'PharmaCure2\includes'; // Adjusted path ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
</head>
<body>
    <h1>Admin Login</h1>
    <form action="admin_login.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br>

        <input type="submit" value="Login">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Fetch user from database
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE email = ? AND user_id IN (SELECT user_id FROM User_Role WHERE role_id = 1)");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password_hash'])) {
            // Start session and store admin information
            $_SESSION['user_id'] = $admin['user_id'];
            $_SESSION['name'] = $admin['name'];
            $_SESSION['role'] = 'admin'; // Store role for access control
            header("Location: admin_dashboard.php"); // Redirect to admin dashboard
            exit();
        } else {
            echo "Invalid email or password.";
        }
    }
    ?>
</body>
</html>