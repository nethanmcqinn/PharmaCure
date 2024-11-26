<?php 
session_start(); // Start session

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]); // Remove item from cart
    }
}
?>