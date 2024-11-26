<?php 
session_start(); // Start the session
include '../config/db.php'; // Include your database connection

// Check if product ID is provided
if (!isset($_GET['id'])) {
    header("Location: products.php"); // Redirect if no product ID is provided
    exit();
}

$productId = $_GET['id'];

// Fetch product details from the database
$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if product exists
if (!$product) {
    header("Location: products.php"); // Redirect if product not found
    exit();
}

// Handle adding product to cart
if (isset($_POST['add_to_cart'])) {
    $quantity = $_POST['quantity'] ?? 1; // Default quantity is 1

    // Initialize cart in session if it doesn't exist
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add or update product in cart
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity; // Update quantity if already in cart
    } else {
        $_SESSION['cart'][$productId] = $quantity; // Add new product to cart
    }
    
    $successMessage = "Product added to cart!";
}

// Fetch reviews for this product
$stmtReviews = $pdo->prepare("SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id = u.user_id WHERE r.product_id = ?");
$stmtReviews->execute([$productId]);
$reviews = $stmtReviews->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<?php include '../includes/navbar.php'; ?> <!-- Include your navbar -->

<div class="container mt-4">
    <h1><?php echo htmlspecialchars($product['name']); ?></h1>

    <div class="row">
        <div class="col-md-6">
            <img src="<?php echo htmlspecialchars($product['main_image']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="col-md-6">
            <h3>Price: $<?php echo htmlspecialchars($product['price']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            <p>Stock Quantity: <?php echo htmlspecialchars($product['stock_quantity']); ?></p>
            
            <!-- Add to Cart Form -->
            <form action="product_details.php?id=<?php echo $productId; ?>" method="POST">
                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                </div>
                <button type="submit" name="add_to_cart" class="btn btn-primary">Add to Cart</button>
            </form>

            <?php if (isset($successMessage)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Display Reviews -->
    <h3>Reviews</h3>
    <?php if (count($reviews) > 0): ?>
        <?php foreach ($reviews as $review): ?>
            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($review['name']); ?></h5>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                    <p class="card-text"><small class="text-muted">Reviewed on <?php echo htmlspecialchars($review['created_at']); ?></small></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No reviews yet.</p>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="../cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="../stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>