<?php 
session_start(); // Start the session
include '../config/db.php'; // Include your database connection

// Initialize search variable
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// Fetch all products from the database based on search query
if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ?");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM products");
}
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<?php include '../includes/navbar.php'; ?> <!-- Include your navbar -->

<div class="container mt-4">
    <h1>Products</h1>

    <!-- Search Form -->
    <form action="products.php" method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search for products..." value="<?php echo htmlspecialchars($search); ?>">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit">Search</button>
            </div>
        </div>
    </form>

    <div class="row">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($product['main_image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="card-text">$<?php echo htmlspecialchars($product['price']); ?></p>
                            <a href="product_details.php?id=<?php echo $product['product_id']; ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products found.</p>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="../cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="../stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>