<!-- index.php -->
 
<?php include '../config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product List</title>
</head>
<body>
    <h1>Product List</h1>
    <a href="product_create.php">Add New Product</a>
    <table border="1">
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Stock Quantity</th>
            <th>Main Image</th>
            <th>Actions</th>
        </tr>

        <?php
        $stmt = $pdo->query("SELECT * FROM Products");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
            echo "<td>" . htmlspecialchars($row['price']) . "</td>";
            echo "<td>" . htmlspecialchars($row['stock_quantity']) . "</td>";
            echo "<td><img src='" . htmlspecialchars($row['main_image']) . "' width='100'></td>";
            echo "<td><a href='product_edit.php?id=".$row['product_id']."'>Edit</a> | 
                  <a href='product_delete.php?id=".$row['product_id']."'>Delete</a></td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>