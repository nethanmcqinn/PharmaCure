<?php 
include '../config/db.php'; 
 //include '../includes/navbar.php';


// Check if product ID is provided
if (!isset($_GET['id'])) {
    header("Location: index.php"); // Redirect to index if no ID is provided
    exit();
}

$product_id = $_GET['id'];

// Fetch product details from the database
$stmt = $pdo->prepare("SELECT * FROM Products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "Product not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - PharmaCure</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">PharmaCure</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="../public/index.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../public/user_login.php">Login</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../public/user_register.php">Register</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../admin/admin_login.php">Admin Login</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-4">
    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
    <img src="<?php echo htmlspecialchars($product['main_image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid mb-3">
    
    <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
    <p><strong>Price:</strong> $<?php echo htmlspecialchars($product['price']); ?></p>
    <p><strong>Stock Quantity:</strong> <?php echo htmlspecialchars($product['stock_quantity']); ?></p>

    <!-- Link back to the product list -->
    <a href="../public/index.php" class="btn btn-primary">Back to Products</a>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>