<!-- user_register.php -->
<?php include '../config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
</head>
<body>
    <h1>User Registration</h1>
    <form action="user_register.php" method="POST" enctype="multipart/form-data">
        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br>

        <label for="name">Full Name:</label>
        <input type="text" name="name" required><br>

        <label for="profile_photo">Profile Photo:</label>
        <input type="file" name="profile_photo"><br>

        <input type="submit" value="Register">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
        $name = $_POST['name'];

        // Handle file upload if a profile photo is provided
        $profile_photo = null;
        if (!empty($_FILES["profile_photo"]["name"])) {
            $target_dir = __DIR__ . "/uploads/";
            $profile_photo = $target_dir . basename($_FILES["profile_photo"]["name"]);
            move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $profile_photo);
        }

        // Insert into database
        $stmt = $pdo->prepare("INSERT INTO Users (email, password_hash, name, profile_photo) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$email, $password, $name, $profile_photo])) {
            echo "User registered successfully.";
        } else {
            echo "Error registering user.";
        }
    }
    ?>
</body>
</html>