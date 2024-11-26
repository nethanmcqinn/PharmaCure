<?php 
session_start(); // Start session

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = []; // Initialize cart if it doesn't exist
}

if (isset($_POST['id']) && isset($_POST['qty'])) {
    $id = $_POST['id'];
    $qty = max(0, intval($_POST['qty'])); // Ensure quantity is non-negative

    if ($qty > 0) {
        $_SESSION['cart'][$id] = $qty; // Update quantity in session
    } else {
        unset($_SESSION['cart'][$id]); // Remove item from cart if qty is zero
    }
}
?>