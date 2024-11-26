<?php 
session_start(); // Start the session
include '../config/db.php'; // Include your database connection
include '../includes/navbar.php'; // Include the navigation menu

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: user_login.php"); // Redirect to login if not logged in or not an admin
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

    <h2>Manage Users</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Email</th>
                <th>Name</th>
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
                    <td>
                        <?php if ($user['profile_photo']): ?>
                            <img src="<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Profile Photo" style="max-width: 50px;">
                        <?php else: ?>
                            No Photo
                        <?php endif; ?>
                    </td>
                    <td>
                        <!-- Add links for editing and deleting users -->
                        <a href="edit_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

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
                    <td><img src="<?php echo htmlspecialchars($product['main_image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="max-width: 50px;"></td>
                    <td>
                        <!-- Add links for editing and deleting products -->
                        <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Provide a link to add new users or products -->
    <a href="add_user.php" class="btn btn-primary">Add User</a> 
    <a href="add_product.php" class="btn btn-primary">Add Product</a> 

    <!-- Orders Section -->
    <h2>Customer Orders</h2> <!-- Ensure this section is clearly separated -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Order Date</th>
                <th>Actions</th> <!-- Actions column for canceling orders -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                    <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['created_at']); ?></td>

                    <!-- Cancel Order Form -->
                    <td><!-- Form to cancel an order -->
                        <form method="POST" action="">
                            <input type="hidden" name="cancel_order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Cancel Order</button> <!-- Button to cancel order -->
                        </form></td>

                </tr>

            <?php endforeach; ?>
        </tbody>

        <?php if (empty($orders)): ?>
            <tr><td colspan="4">No orders found.</td></tr> <!-- Message if no orders exist -->
        <?php endif; ?>

    </table>

    
	<!-- Reviews Section -->
	<h2>User Reviews</h2> 
	<table class="table table-striped">
	    <thead>
	        <tr> 
	            <!-- Review Table Headers --> 
	            <th>Review ID</th> 
	            <th>Product Name</th> 
	            <th>User Name</th> 
	            <th>Review Text</th> 
	            <th>Created At</th> 
	        </tr> 
	    </thead> 
	    <tbody> 
	        <?php if (count($reviews) > 0): ?> 
	            <?php foreach ($reviews as $review): ?> 
	                <tr> 
	                    <!-- Review Data --> 
	                    <td><?php echo htmlspecialchars($review['review_id']); ?></td> 
	                    <td><?php echo htmlspecialchars($review['product_name']); ?></td> 
	                    <td><?php echo htmlspecialchars($review['user_name']); ?></td> 
	                    <!-- Display review text with line breaks --> 
	                    <td><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></td> 
	                    <!-- Display created date --> 
	                    <td><?php echo htmlspecialchars($review['created_at']); ?></td> 
	                </tr> 
	            <?php endforeach; ?> 
	        <?php else: ?> 
	            <!-- Message if no reviews exist --> 
	            <tr><td colspan="5">No reviews found.</td></tr>  
	        <?php endif; ?>  
	    </tbody>  
	</table>

    
	
    
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>  
	<script src="../cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>  
	<script src="../stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>  
</html>
