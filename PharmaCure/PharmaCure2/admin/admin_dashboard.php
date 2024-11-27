<?php 
session_start(); // Start the session
include '../config/db.php'; // Include your database connection
include '../includes/navbar.php'; // Include the navigation menu

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('You do not have access to this page.'); window.location.href = 'user_login.php';</script>";
    exit();
}

// Fetch users from the database
$stmtUsers = $pdo->query("SELECT * FROM users");
$users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

// Fetch products from the database
$stmtProducts = $pdo->query("SELECT * FROM products");
$products = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);

// Fetch reviews with product and user details
$stmtReviews = $pdo->prepare("
    SELECT r.review_id, r.review_text, r.created_at, p.name AS product_name, u.name AS user_name 
    FROM reviews r 
    JOIN products p ON r.product_id = p.product_id 
    JOIN users u ON r.user_id = u.user_id
");
$stmtReviews->execute();
$reviews = $stmtReviews->fetchAll(PDO::FETCH_ASSOC);

// Fetch orders from the database
$stmtOrders = $pdo->prepare("
    SELECT o.order_id, o.created_at, u.name AS user_name 
    FROM orders o 
    JOIN users u ON o.user_id = u.user_id 
    ORDER BY o.created_at DESC
");
$stmtOrders->execute();
$orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

// Handle order cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order_id'])) {
    $cancelOrderId = $_POST['cancel_order_id'];

    // Cancel the order (you might want to implement a status column in your orders table)
    $stmtCancel = $pdo->prepare("DELETE FROM orders WHERE order_id = ?");
    $stmtCancel->execute([$cancelOrderId]);

    // Optionally, delete related items from order_items table
    $stmtDeleteItems = $pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
    $stmtDeleteItems->execute([$cancelOrderId]);

    header("Location: admin_dashboard.php"); // Redirect back to the dashboard after cancellation
    exit();
}

// Handle review deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_review_id'])) {
    $deleteReviewId = $_POST['delete_review_id'];

    // Delete the review from the database
    $stmtDeleteReview = $pdo->prepare("DELETE FROM reviews WHERE review_id = ?");
    $stmtDeleteReview->execute([$deleteReviewId]);

    header("Location: admin_dashboard.php"); // Redirect back to the dashboard after deletion
    exit();
}

// Handle user status and role updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id']) && isset($_POST['action'])) {
        $userId = $_POST['user_id'];
        
        if ($_POST['action'] === 'deactivate') {
            // Deactivate the user
            $stmtDeactivate = $pdo->prepare("UPDATE users SET status = 'deactivated' WHERE user_id = ?");
            $stmtDeactivate->execute([$userId]);
        } elseif ($_POST['action'] === 'activate') {
            // Activate the user
            $stmtActivate = $pdo->prepare("UPDATE users SET status = 'active' WHERE user_id = ?");
            $stmtActivate->execute([$userId]);
        } elseif ($_POST['action'] === 'update_role' && isset($_POST['new_role'])) {
            // Update the user's role
            $newRole = $_POST['new_role'];
            $stmtUpdateRole = $pdo->prepare("UPDATE users SET role = ? WHERE user_id = ?");
            $stmtUpdateRole->execute([$newRole, $userId]);
        }

        header("Location: admin_dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1>Admin Dashboard</h1>

    <!-- Manage Users -->
    <h2>Manage Users</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Email</th>
                <th>Name</th>
                <th>Role</th>
                <th>Status</th>
                <th>Profile Photo</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                    <td>
                        <span class="badge badge-<?php echo ($user['status'] ?? 'inactive') === 'active' ? 'success' : 'secondary'; ?>">
                            <?php echo ucfirst($user['status'] ?? 'inactive'); ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($user['profile_photo']): ?>
                            <img src="<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Profile Photo" style="max-width: 50px;">
                        <?php else: ?>
                            No Photo
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                            <?php if ($user['status'] === 'active'): ?>
                                <button type="submit" name="action" value="deactivate" class="btn btn-warning btn-sm">Deactivate</button>
                            <?php else: ?>
                                <button type="submit" name="action" value="activate" class="btn btn-success btn-sm">Activate</button>
                            <?php endif; ?>
                        </form>
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                            <select name="new_role" class="form-control form-control-sm d-inline" style="width:auto; display:inline;">
                                <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                            <button type="submit" name="action" value="update_role" class="btn btn-primary btn-sm">Update Role</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Manage Products -->
    <h2>Manage Products</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Stock Quantity</th>
                <th>Main Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['product_id']); ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['description']); ?></td>
                    <td>$<?php echo htmlspecialchars($product['price']); ?></td>
                    <td><?php echo htmlspecialchars($product['stock_quantity']); ?></td>
                    <td>
                        <img src="<?php echo htmlspecialchars($product['main_image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="max-width: 50px;">
                    </td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Link for Adding -->
    <a href="add_user.php" class="btn btn-primary">Add User</a> 
    <a href="add_product.php" class="btn btn-primary">Add Product</a> 

    <!-- Orders Section -->
    <h2>Customer Orders</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>User</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                    <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="cancel_order_id" value="<?php echo $order['order_id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Reviews Section -->
    <h2>Customer Reviews</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Review ID</th>
                <th>User</th>
                <th>Product</th>
                <th>Review Text</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reviews as $review): ?>
                <tr>
                    <td><?php echo htmlspecialchars($review['review_id']); ?></td>
                    <td><?php echo htmlspecialchars($review['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($review['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($review['review_text']); ?></td>
                    <td><?php echo htmlspecialchars($review['created_at']); ?></td>
                    <td>
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="delete_review_id" value="<?php echo $review['review_id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>

</body>
</html>
