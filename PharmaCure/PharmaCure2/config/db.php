<?php
$host = 'localhost';
$db = 'PharmaCureDB';
$user = 'root'; // replace with your MySQL username
$pass = ''; // replace with your MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>