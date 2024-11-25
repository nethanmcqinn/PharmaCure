<?php 
session_start(); 
include '../config/db.php'; 
include '../includes/functions.php'; // Ensure this path is correct

// Check if the user has permission to view the admin dashboard
if (!hasPermission('view_admin_dashboard')) { 
    header("Location: admin_login.php"); 
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welcome to Admin Dashboard</h1>
    <p>Hello, <?php echo htmlspecialchars($_SESSION['name']); ?>!</p>
    
    <nav>
        <ul>
            <li><a href="manage_products.php">Manage Products</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="../public/user_logout.php">Logout</a></li> <!-- Link to logout script -->
        </ul>
    </nav>

    <!-- Additional dashboard content can go here -->
</body>
</html>