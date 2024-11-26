<?php 
session_start(); 
<?php include '../config/db.php'; ?>
<?php include '../includes/functions.php'; // Adjusted path ?>

if (!hasPermission('manage_products')) { // Check for appropriate permission
    header("Location: admin_login.php"); 
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products</title>
</head>
<body>

<h1>Manage Products</h1>

<a href="../public/product_create.php">Add New Product</a> <!-- Link to product creation -->

<table border="1">
    <tr>
        <th>Name</th>
        <th>Description</th>
        <th>Price</th>
        <th>Stock Quantity</th>
        <th>Actions</th>
    </tr>

    <?php
    // Fetch products from the database
    $stmt = $pdo->query("SELECT * FROM Products");
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
        echo "<td>" . htmlspecialchars($row['price']) . "</td>";
        echo "<td>" . htmlspecialchars($row['stock_quantity']) . "</td>";
        
        // Actions: Edit and Delete links
        echo "<td><a href='../public/product_edit.php?id=".$row['product_id']."'>Edit</a> | 
              <a href='../public/product_delete.php?id=".$row['product_id']."'>Delete</a></td>";
        
        echo "</tr>";
    }
    ?>
</table>

</body>
</html>