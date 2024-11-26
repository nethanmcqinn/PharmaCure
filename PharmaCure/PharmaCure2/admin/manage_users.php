<?php 
session_start(); 
<?php include '../config/db.php'; ?>
<?php include '../includes/functions.php'; // Adjusted path ?>

if (!hasPermission('manage_users')) { // Check for appropriate permission
    header("Location: admin_login.php"); 
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
</head>
<body>

<h1>Manage Users</h1>

<table border="1">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Actions</th>
    </tr>

    <?php
    // Fetch users from the database
    $stmt = $pdo->query("SELECT u.user_id, u.name, u.email, r.role_name FROM Users u JOIN User_Role ur ON u.user_id = ur.user_id JOIN Roles r ON ur.role_id = r.role_id");
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        
        // Display user role (assuming you have a roles table)
        echo "<td>" . htmlspecialchars($row['role_name']) . "</td>";
        
        // Actions: Edit and Delete links (you can implement these as needed)
        echo "<td><a href='edit_user.php?id=".$row['user_id']."'>Edit</a> | 
              <a href='delete_user.php?id=".$row['user_id']."'>Delete</a></td>";
        
        echo "</tr>";
    }
    ?>
</table>

<a href="../public/user_logout.php">Logout</a>

</body>
</html>