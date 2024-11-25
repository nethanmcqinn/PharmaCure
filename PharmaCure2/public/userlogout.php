<!-- user_logout.php -->
<?php include '../includes/navbar.php'; ?>

<?php
session_start();
session_destroy(); // Destroy all session data
header("Location: user_login.php"); // Redirect to login page
exit();
?>