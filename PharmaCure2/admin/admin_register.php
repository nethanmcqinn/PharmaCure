<?php include '../config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Registration</title>
</head>
<body>
    <h1>Admin Registration</h1>
    <form action="admin_register.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br>

        <label for="name">Full Name:</label>
        <input type="text" name="name" required><br>

        <input type="submit" value="Register Admin">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
        $name = $_POST['name'];

        // Check if email already exists
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM Users WHERE email = ?");
        $stmtCheck->execute([$email]);
        $emailExists = $stmtCheck->fetchColumn();

        if ($emailExists) {
            echo "This email is already registered. Please use a different email.";
        } else {
            // Insert into Users table
            $stmt = $pdo->prepare("INSERT INTO Users (email, password_hash, name) VALUES (?, ?, ?)");
            
            if ($stmt->execute([$email, $password, $name])) {
                // Get the last inserted user ID
                $userId = $pdo->lastInsertId();

                // Assign admin role (assuming '1' is the ID for 'admin' role)
                $roleId = 1; // Change this based on your roles setup
                $stmtRole = $pdo->prepare("INSERT INTO User_Role (user_id, role_id) VALUES (?, ?)");
                if ($stmtRole->execute([$userId, $roleId])) {
                    echo "Admin registered successfully.";
                } else {
                    echo "Error assigning admin role.";
                }
            } else {
                echo "Error registering admin.";
            }
        }
    }
    ?>
</body>
</html>