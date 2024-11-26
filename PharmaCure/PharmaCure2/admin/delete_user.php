<?php 
session_start(); // Start the session
include '../config/db.php'; // Include your database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: user_login.php"); // Redirect to login if not logged in or not an admin
    exit();
}

// Check if user ID is provided
if (!isset($_GET['id'])) {
    header("Location: admin_dashboard.php"); // Redirect if no user ID is provided
    exit();
}

$userId = $_GET['id'];

// Delete user from the database
$stmtDelete = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
if ($stmtDelete->execute([$userId])) {
    header("Location: admin_dashboard.php?message=User deleted successfully.");
} else {
    header("Location: admin_dashboard.php?error=Failed to delete user.");
}
?>