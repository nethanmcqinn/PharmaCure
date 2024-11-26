<?php include '../config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
</head>
<body>
    <h1>Add New Product</h1>
    <form action="product_create.php" method="POST" enctype="multipart/form-data">
        <label for="name">Product Name:</label>
        <input type="text" name="name" required><br>

        <label for="description">Description:</label>
        <textarea name="description" required></textarea><br>

        <label for="price">Price:</label>
        <input type="number" step="0.01" name="price" required><br>

        <label for="stock_quantity">Stock Quantity:</label>
        <input type="number" name="stock_quantity" required><br>

        <label for="main_image">Main Image:</label>
        <input type="file" name="main_image" required><br>

        <input type="submit" value="Add Product">
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $stock_quantity = $_POST['stock_quantity'];

        $target_dir = __DIR__ . "/uploads/"; // Absolute path
        $target_file = $target_dir . basename($_FILES["main_image"]["name"]);

        // Check for upload errors
        if ($_FILES["main_image"]["error"] !== UPLOAD_ERR_OK) {
            echo "Error during file upload: " . $_FILES["main_image"]["error"];
            exit;
        }

        // Additional checks
        if (!is_dir($target_dir)) {
            echo "Target directory does not exist.";
            exit;
        }

        if (!is_writable($target_dir)) {
            echo "Target directory is not writable.";
            exit;
        }

        // Attempt to move the uploaded file
        if (move_uploaded_file($_FILES["main_image"]["tmp_name"], $target_file)) {
            $stmt = $pdo->prepare("INSERT INTO Products (name, description, price, stock_quantity, main_image) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $description, $price, $stock_quantity, $target_file])) {
                echo "Product added successfully.";
            } else {
                echo "Error adding product.";
            }
        } else {
            echo "Error uploading file.";
            // Additional error checking
            if (!file_exists($target_dir)) {
                echo "Target directory does not exist.";
            }
            if (!is_writable($target_dir)) {
                echo "Target directory is not writable.";
            }
        }
    }
    ?>
</body>
</html>