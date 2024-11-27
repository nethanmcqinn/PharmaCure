<?php 
session_start(); // Start the session
include '../config/db.php'; // Include your database connection

// Initialize search variable
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// Fetch all products from the database based on search query along with categories and brands
if ($search) {
    $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name, b.name AS brand_name 
                            FROM products p 
                            LEFT JOIN categories c ON p.category_id = c.category_id 
                            LEFT JOIN brands b ON p.brand_id = b.brand_id 
                            WHERE p.name LIKE ? OR p.description LIKE ?");
    $stmt->execute(["%$search%", "%$search%"]);
} else {
    $stmt = $pdo->query("SELECT p.*, c.name AS category_name, b.name AS brand_name 
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.category_id 
                          LEFT JOIN brands b ON p.brand_id = b.brand_id");
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
    <style>
        /* Custom styles for 3D product cards */
        .product-card {
            background-color: #ffffff;
            border-radius: 10px; /* Rounded corners */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
            transition: transform 0.3s, box-shadow 0.3s; /* Smooth transition for hover effect */
            margin-bottom: 20px; /* Space between cards */
        }

        .product-card:hover {
            transform: translateY(-5px); /* Lift effect on hover */
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2); /* Enhanced shadow on hover */
        }

        .product-image {
            border-top-left-radius: 10px; /* Match card's rounded corners */
            border-top-right-radius: 10px; /* Match card's rounded corners */
        }

        .product-title {
            font-size: 1.5em; /* Larger font size for title */
        }

        .product-price {
            color: #28a745; /* Green color for price */
            font-weight: bold;
        }
    </style>
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
                    <div class="product-card"> <!-- Use the new product-card class -->
                        <img src="<?php echo htmlspecialchars($product['main_image']); ?>" class="card-img-top product-image" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title product-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="card-text product-price">$<?php echo htmlspecialchars($product['price']); ?></p>
                            <p class="card-text"><strong>Category:</strong> <?php echo htmlspecialchars($product['category_name']); ?></p>
                            <p class="card-text"><strong>Brand:</strong> <?php echo htmlspecialchars($product['brand_name']); ?></p>
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
