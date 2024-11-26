<?php 
session_start(); // Start the session
include '../config/db.php'; 
include '../includes/navbar.php'; // Include the navigation menu

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php"); // Redirect to login if not logged in
    exit();
}

// Fetch user details from the database
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM Users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit();
}

// Handle profile update when form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $profile_photo = $user['profile_photo']; // Keep current photo if not updated

    // Handle file upload if a new profile photo is provided
    if (!empty($_FILES["profile_photo"]["name"])) {
        $target_dir = '../uploads/'; // Ensure this directory exists and is writable
        $profile_photo = $target_dir . basename($_FILES["profile_photo"]["name"]);
        if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $profile_photo)) {
            // File uploaded successfully
        } else {
            echo "<div class='alert alert-warning'>Error uploading profile photo.</div>";
        }
    }

    // Update user details in the database
    $stmtUpdate = $pdo->prepare("UPDATE Users SET name = ?, profile_photo = ? WHERE user_id = ?");
    if ($stmtUpdate->execute([$name, $profile_photo, $userId])) {
        echo "<div class='alert alert-success'>Profile updated successfully.</div>";
        // Refresh user data
        $_SESSION['name'] = $name; // Update session name
        header("Location: user_profile.php"); // Redirect to self to refresh data
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error updating profile.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h1>User Profile</h1>

    <form action="user_profile.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Full Name:</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="profile_photo">Profile Photo:</label>
            <?php if ($user['profile_photo']): ?>
                <img src="<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Profile Photo" class="img-fluid mb-3" style="max-width: 200px;">
            <?php endif; ?>
            <input type="file" name="profile_photo" class="form-control-file">
        </div>

        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="../cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="../stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>