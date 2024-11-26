<?php 
session_start(); // Start the session
include '../config/db.php'; // Include your database connection
include '../includes/navbar.php';
// Process user login when form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user from the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify user and password
    if ($user && password_verify($password, $user['password_hash'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role']; // Store role ID

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: ../admin/admin_dashboard.php");
        } else {
            header("Location: user_profile.php");
        }
        exit();
    } else {
        $loginError = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h1>User Login</h1>

    <?php if (isset($loginError)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($loginError); ?></div>
    <?php endif; ?>

    <form action="user_login.php" method="POST">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Login</button>
    </form>

    <p class="mt-3">Don't have an account? <a href="../public/user_register.php">Register here</a>.</p>
    
    <!-- Link to Admin Login -->
    <p>Are you an admin? <a href="../admin/admin_login.php">Login here</a>.</p> <!-- Link to Admin Login -->
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="../cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="../stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>