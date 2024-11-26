<!-- product_edit.php -->
<?php include '../config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
</head>
<body>

<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Fetch product details
    $stmt = $pdo->prepare("SELECT * FROM Products WHERE product_id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        die("Product not found.");
    }
?>

<h1>Edit Product</h1>
<form action="" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['product_id']); ?>">
    
    <label for="name">Product Name:</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required><br>

    <label for="description">Description:</label>
    <textarea name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea><br>

    <label for="price">Price:</label>
    <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required><br>

    <label for="stock_quantity">Stock Quantity:</label>
    <input type="number" name="stock_quantity" value="<?php echo htmlspecialchars($product['stock_quantity']); ?>" required><br>

    <label for="main_image">Main Image:</label><br/>
    Current Image: <img src="<?php echo htmlspecialchars($product['main_image']); ?>" width='100'><br/>
    Upload New Image (optional): 
    <input type="file" name="main_image"><br/>

    <input type="submit" value="Update Product">
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    
    // Handle file upload if new image is provided
    if (!empty($_FILES["main_image"]["name"])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["main_image"]["name"]);
        
        if (move_uploaded_file($_FILES["main_image"]["tmp_name"], $target_file)) {
            // Update in database with new image path
            $stmt = $pdo->prepare("UPDATE Products SET name=?, description=?, price=?, stock_quantity=?, main_image=? WHERE product_id=?");
            if ($stmt->execute([$name, $description, $price, $stock_quantity, $target_file, $_POST['id']])) {
                echo "Product updated successfully.";
            } else {
                echo "Error updating product.";
            }
        } else {
            echo "Error uploading file.";
        }
    } else {
        // Update in database without changing the image
        $stmt = $pdo->prepare("UPDATE Products SET name=?, description=?, price=?, stock_quantity=? WHERE product_id=?");
        if ($stmt->execute([$name, $description, $price, $stock_quantity, $_POST['id']])) {
            echo "Product updated successfully.";
        } else {
            echo "Error updating product.";
        }
   }
}
?>

<?php } ?>
</body>
</html>