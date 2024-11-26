<?php 
session_start(); // Start the session

// Check if there are items in the cart
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: products.php"); // Redirect if the cart is empty
    exit();
}

// Include database connection
include '../config/db.php'; 

// Handle item removal if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_id'])) {
        // Remove item from cart
        $removeId = $_POST['remove_id'];
        if (isset($_SESSION['cart'][$removeId])) {
            unset($_SESSION['cart'][$removeId]);
        }
        // Redirect to the same page to refresh the cart
        header("Location: cart.php");
        exit();
    } elseif (isset($_POST['update_id']) && isset($_POST['quantity'])) {
        // Update item quantity
        $updateId = $_POST['update_id'];
        $newQuantity = (int)$_POST['quantity'];
        
        // Ensure the new quantity is at least 1
        if ($newQuantity > 0 && isset($_SESSION['cart'][$updateId])) {
            $_SESSION['cart'][$updateId] = $newQuantity;
        }

        // Redirect to the same page to refresh the cart
        header("Location: cart.php");
        exit();
    }
}

// Fetch product details for items in the cart
$cartItems = $_SESSION['cart'];
$productIds = implode(',', array_keys($cartItems)); // Get product IDs as a comma-separated string

$stmt = $pdo->query("SELECT * FROM products WHERE product_id IN ($productIds)");
$productsInCart = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<?php include '../includes/navbar.php'; ?> <!-- Include your navbar -->

<div class="container mt-4">
    <h1>Your Shopping Cart</h1>

    <table class="table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
                <th>Actions</th> <!-- Add Actions column -->
            </tr>
        </thead>
        <tbody>
            <?php 
            $totalAmount = 0;
            foreach ($productsInCart as $product): 
                $quantity = $cartItems[$product['product_id']];
                $totalPrice = $quantity * $product['price'];
                $totalAmount += $totalPrice;
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td>
                        <!-- Quantity Update Form -->
                        <form method="POST" action="cart.php" class="form-inline">
                            <input type="number" name="quantity" value="<?php echo htmlspecialchars($quantity); ?>" min="1" class="form-control mr-2" style="width: 80px;">
                            <input type="hidden" name="update_id" value="<?php echo $product['product_id']; ?>">
                            <button type="submit" class="btn btn-primary btn-sm">Update</button>
                        </form>
                    </td>
                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                    <td>$<?php echo number_format($totalPrice, 2); ?></td>
                    <td>
                        <!-- Remove Button Form -->
                        <form method="POST" action="cart.php">
                            <input type="hidden" name="remove_id" value="<?php echo $product['product_id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right"><strong>Total Amount:</strong></td>
                <td>$<?php echo number_format($totalAmount, 2); ?></td>
            </tr>
        </tfoot>
    </table>

    <!-- Order Submission Form -->
    <form action="checkout.php" method="POST">
        <button type="submit" class="btn btn-success">Proceed to Checkout</button> <!-- Button to proceed to checkout -->
    </form>

</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
