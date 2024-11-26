<?php 
session_start(); // Start the session
include '../config/db.php'; // Include your database connection
include '../includes/navbar.php'; // Include the navigation menu

// Check if product ID is provided
if (!isset($_GET['id'])) {
    header("Location: products.php"); // Redirect if no product ID is provided
    exit();
}

$productId = $_GET['id'];

// Fetch product details from the database
$stmtProduct = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
$stmtProduct->execute([$productId]);
$product = $stmtProduct->fetch(PDO::FETCH_ASSOC);

// Check if product exists
if (!$product) {
    header("Location: products.php"); // Redirect if product not found
    exit();
}

// Process review submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo "<div class='alert alert-danger'>You must be logged in to submit a review.</div>";
    } else {
        $userId = $_SESSION['user_id']; // Get logged-in user's ID
        $reviewText = trim($_POST['review_text']);

        // Insert review into the database
        $stmtReview = $pdo->prepare("INSERT INTO reviews (product_id, user_id, review_text) VALUES (?, ?, ?)");
        
        if ($stmtReview->execute([$productId, $userId, $reviewText])) {
            echo "<div class='alert alert-success'>Review added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error adding review. Please try again.</div>";
        }
    }
}

// Fetch existing reviews for this product
$stmtReviews = $pdo->prepare("
    SELECT r.review_id, r.review_text, r.created_at, u.name AS user_name 
    FROM reviews r 
    JOIN users u ON r.user_id = u.user_id 
    WHERE r.product_id = ?
");
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

    <!-- Review Submission Form -->
    <h2>Add Your Review</h2>
    <form action="product_details.php?id=<?php echo $productId; ?>" method="POST">
        <div class="form-group">
            <label for="review_text">Your Review:</label>
            <textarea name="review_text" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Review</button>
    </form>

    <!-- Display Existing Reviews -->
    <h3>Customer Reviews</h3>
    <?php if (count($reviews) > 0): ?>
        <?php foreach ($reviews as $review): ?>
            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($review['user_name']); ?></h5>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                    <p class="card-text"><small class="text-muted">Reviewed on <?php echo htmlspecialchars($review['created_at']); ?></small></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No reviews yet. Be the first to review this product!</p>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="../cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script> 
<script src="../stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body> 
</html>