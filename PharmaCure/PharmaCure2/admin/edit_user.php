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

// Fetch user details from the database
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if user exists
if (!$user) {
    header("Location: admin_dashboard.php"); // Redirect if user not found
    exit();
}

// Process form submission when editing user
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $name = $_POST['name'];
    $isActive = isset($_POST['is_active']) ? 1 : 0; // Checkbox for active status

    // Update user in the database
    $stmtUpdate = $pdo->prepare("UPDATE users SET email = ?, name = ?, is_active = ? WHERE user_id = ?");
    if ($stmtUpdate->execute([$email, $name, $isActive, $userId])) {
        $success = "User updated successfully!";
    } else {
        $error = "Failed to update user. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h1>Edit User</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form action="edit_user.php?id=<?php echo $userId; ?>" method="POST">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>

        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        </div>

        <div class="form-group form-check">
            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" <?php echo $user['is_active'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="is_active">Active</label>
        </div>

        <button type="submit" class="btn btn-primary">Update User</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="../cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="../stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>