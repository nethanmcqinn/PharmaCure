<!-- product_delete.php -->
<?php include '../config/db.php'; ?>

<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete the product from the database
    $stmt = $pdo->prepare("DELETE FROM Products WHERE product_id = ?");
    
    if ($stmt->execute([$id])) {
       header("Location: index.php");
       exit();
   } else {
       echo "Error deleting product.";
   }
}
?>