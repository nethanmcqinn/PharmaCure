<?php 
session_start(); // Start the session

// Check if there are items in the cart
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: products.php"); // Redirect if the cart is empty
    exit();
}

// Include database connection
include '../config/db.php'; 

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id']; // Get logged-in user ID
    $cartItems = $_SESSION['cart'];

    // Insert order into the database
    $stmtOrder = $pdo->prepare("INSERT INTO orders (user_id, created_at) VALUES (?, NOW())");
    $stmtOrder->execute([$userId]);
    $orderId = $pdo->lastInsertId(); // Get the last inserted order ID

    // Insert each item into the order_items table
    foreach ($cartItems as $productId => $quantity) {
        $stmtOrderItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmtOrderItem->execute([$orderId, $productId, $quantity]);
    }

    // Clear the cart after placing the order
    unset($_SESSION['cart']);

    // Redirect to confirmation page
    header("Location: order_confirmation.php?id=" . $orderId);
    exit();
}

// Fetch product details for items in the cart
$productIds = implode(',', array_keys($_SESSION['cart'])); // Get product IDs as a comma-separated string
$stmt = $pdo->query("SELECT * FROM products WHERE product_id IN ($productIds)");
$productsInCart = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<?php include '../includes/navbar.php'; ?> <!-- Include your navbar -->

<div class="container mt-4">
    <h1>Checkout</h1>

    <h2>Your Cart Items</h2>
    
	<table class="table">
	    <thead>
	        <tr>
	            <th>Product</th>
	            <th>Quantity</th>
	            <th>Price</th>
	            <th>Total</th>
	        </tr>
	    </thead>
	    <tbody>
	        <?php 
	        $totalAmount = 0;
	        foreach ($productsInCart as $product): 
	            // Set variables to use in content below
	            $product_name = htmlspecialchars($product["name"]);
	            $product_qty = $_SESSION['cart'][$product["product_id"]];
	            $product_price = (float)$product["price"]; // Ensure price is treated as a float
	            $subtotal = ($product_price * $product_qty); // Calculate Price x Qty
	            $totalAmount += $subtotal;
	        ?>
	            <tr>
	                <td><?php echo $product_name; ?></td>
	                <td><?php echo htmlspecialchars($product_qty); ?></td>
	                <td>$<?php echo number_format($product_price, 2); ?></td>
	                <td>$<?php echo number_format($subtotal, 2); ?></td> <!-- Calculate total price per item -->
	            </tr>
	        <?php endforeach; ?>
	    </tbody>
	    <tfoot>
	        <tr>
	            <td colspan="3" class="text-right"><strong>Total Amount:</strong></td>
	            <td>$<?php echo number_format($totalAmount, 2); ?></td> <!-- Display total amount -->
	        </tr>
	    </tfoot>
	</table>

	<!-- Order Submission Form -->
	<form action="" method="POST"> <!-- POST method for placing an order -->
	    <button type="submit" class="btn btn-success">Place Order</button> <!-- Button to place order -->
	</form>

</div>

<script src="../cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script> 
<script src="../stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body> 
</html> 