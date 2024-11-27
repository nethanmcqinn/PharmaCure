<?php 
session_start(); // Start the session
include '../config/db.php'; 
include '../includes/navbar.php'; // Include the navigation menu
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaCure - Home</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles.css"> <!-- Link to your custom CSS file -->
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

<div class="container mt-4">
    <h1>Welcome to PharmaCure</h1>

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="alert alert-success">
            Hello, <?php echo htmlspecialchars($_SESSION['name']); ?>! Welcome back to PharmaCure.
        </div>
    <?php else: ?>
        <p>Your one-stop shop for pharmaceutical products.</p>
    <?php endif; ?>

    <h2>Featured Products</h2>
    <div class="row">

        <?php
        // Fetch products from the database along with categories and brands
        $stmt = $pdo->query("SELECT p.*, c.name, b.name FROM products p 
                              LEFT JOIN categories c ON p.category_id = c.category_id 
                              LEFT JOIN brands b ON p.brand_id = b.brand_id 
                              LIMIT 6"); // Adjust limit as necessary
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="col-md-4">';
            echo '<div class="product-card">'; // Use the new product-card class
            echo '<img src="' . htmlspecialchars($row['main_image']) . '" class="card-img-top product-image" alt="' . htmlspecialchars($row['name']) . '">';
            echo '<div class="card-body">';
            echo '<h5 class="card-title product-title">' . htmlspecialchars($row['name']) . '</h5>';
            echo '<p class="card-text">' . htmlspecialchars($row['description']) . '</p>';
            echo '<p class="card-text product-price">Price: $' . htmlspecialchars($row['price']) . '</p>';
            echo '<p class="card-text"><strong>Category:</strong> ' . htmlspecialchars($row['name']) . '</p>';
            echo '<p class="card-text"><strong>Brand:</strong> ' . htmlspecialchars($row['name']) . '</p>';
            echo '<a href="product_details.php?id=' . $row['product_id'] . '" class="btn btn-primary">View Details</a>'; // Link to product details page
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        ?>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="../cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="../stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>