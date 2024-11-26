<?php 
session_start(); // Start the session
include '../config/db.php'; // Include your database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../user_login.php"); // Redirect to login if not logged in or not an admin
    exit();
}

// Process form submission when adding a new product
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];

    // Handle file upload
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];

        // Specify the directory where the image will be saved
        $uploadFileDir = '../public/uploads/';
        $dest_path = $uploadFileDir . basename($fileName);

        // Move the uploaded file to the specified directory
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $imagePath = $dest_path; // Store the path for database insertion
        } else {
            $error = "There was an error uploading the image.";
        }
    } else {
        $error = "No image uploaded or there was an error with the upload.";
    }

    // Insert new product into the database
    if (empty($error)) {
        $stmtInsert = $pdo->prepare("INSERT INTO products (name, description, price, stock_quantity, main_image, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
        
        if ($stmtInsert->execute([$name, $description, $price, $stock_quantity, $imagePath])) {
            $success = "Product added successfully!";
        } else {
            $error = "Failed to add product. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h1>Add New Product</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form action="add_product.php" method="POST" enctype="multipart/form-data"> <!-- Add enctype for file upload -->
        <div class="form-group">
            <label for="name">Product Name:</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>

        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" step="0.01" name="price" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="stock_quantity">Stock Quantity:</label>
            <input type="number" name="stock_quantity" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="image">Product Image:</label>
            <input type="file" name="image" class="form-control-file" accept=".jpg,.jpeg,.png,.gif"> <!-- Allow specific image types -->
        </div>

        <button type="submit" class="btn btn-primary">Add Product</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="../cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="../stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>