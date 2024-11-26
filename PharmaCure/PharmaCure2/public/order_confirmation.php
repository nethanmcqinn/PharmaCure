<?php 
session_start(); // Start session
include('../config/db.php'); // Include your database connection

// Check if order ID is provided
if (!isset($_GET['id'])) {
    header("Location: products.php"); // Redirect if no order ID is provided
    exit();
}

$orderId = $_GET['id'];

// Fetch order details
$stmtOrderDetails = $pdo->prepare("
    SELECT o.*, u.name AS user_name 
    FROM orders o 
    JOIN users u ON o.user_id = u.user_id 
    WHERE o.order_id = ?
");
$stmtOrderDetails->execute([$orderId]);
$orderDetails = $stmtOrderDetails->fetch(PDO::FETCH_ASSOC);

// Fetch items for this order
$stmtOrderItems = $pdo->prepare("
    SELECT oi.*, p.name AS product_name, p.price 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.product_id 
    WHERE oi.order_id = ?
");
$stmtOrderItems->execute([$orderId]);
$orderItems = $stmtOrderItems->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<?php include('../includes/navbar.php'); ?> <!-- Include your navbar -->

<div class="container mt-4">
    <h1>Order Confirmation</h1>

    <?php if ($orderDetails): ?>
        <h3>Thank you for your order, <?php echo htmlspecialchars($orderDetails['user_name']); ?>!</h3>
        <p>Your order ID is: <?php echo htmlspecialchars($orderId); ?></p>

        <h4>Order Details:</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th> <!-- Add a column for total price -->
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalAmount = 0; // Initialize total amount
                foreach ($orderItems as $item): 
                    // Ensure we fetch price from the database
                    $product_name = htmlspecialchars($item['product_name']);
                    $quantity = (int)htmlspecialchars($item['quantity']);
                    $price = (float)htmlspecialchars($item['price']);
                    $subtotal = $quantity * $price; // Calculate subtotal for this item
                    $totalAmount += $subtotal; // Add to total amount
                ?>
                    <tr>
                        <td><?php echo $product_name; ?></td>
                        <td><?php echo $quantity; ?></td>
                        <td>$<?php echo number_format($price, 2); ?></td>
                        <td>$<?php echo number_format($subtotal, 2); ?></td> <!-- Display subtotal for this item -->
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Total Amount -->
        <h4>Total Amount: $<?php echo number_format($totalAmount, 2); ?></h4>

        <!-- Provide a link back to products or account -->
        <a href="products.php" class="btn btn-primary">Continue Shopping</a>

    <?php else: ?>
        <p>Order not found.</p> <!-- Handle case where no order details are found -->
    <?php endif; ?>
</div>

<script src="../cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script> 
<script src="../stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body> 
</html>