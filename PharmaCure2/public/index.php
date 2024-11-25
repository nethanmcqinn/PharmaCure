<?php include '../config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaCure - Home</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles.css"> <!-- Link to your custom CSS file -->
</head>
<body>

<!-- Include Navigation Menu -->
<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
    <h1>Welcome to PharmaCure</h1>
    <p>Your one-stop shop for pharmaceutical products.</p>

    <h2>Featured Products</h2>
    <div class="row">

        <?php
        // Fetch products from the database
        $stmt = $pdo->query("SELECT * FROM Products LIMIT 6"); // Adjust limit as necessary
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="col-md-4">';
            echo '<div class="card mb-4">';
            echo '<img src="' . htmlspecialchars($row['main_image']) . '" class="card-img-top" alt="' . htmlspecialchars($row['name']) . '">';
            echo '<div class="card-body">';
            echo '<h5 class="card-title">' . htmlspecialchars($row['name']) . '</h5>';
            echo '<p class="card-text">' . htmlspecialchars($row['description']) . '</p>';
            echo '<p class="card-text"><strong>Price: $' . htmlspecialchars($row['price']) . '</strong></p>';
            echo '<a href="product_details.php?id=' . $row['product_id'] . '" class="btn btn-primary">View Details</a>'; // Link to product details page
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        ?>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>