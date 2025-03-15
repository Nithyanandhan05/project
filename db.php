<?php
// db.php

$host = '127.0.0.1';  // Database host, usually localhost
$db = 'gadget_store';  // Your database name
$user = 'root';        // Database username (default is 'root' for XAMPP)
$pass = '';            // Database password (default is empty for XAMPP)

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Optionally, set the character set to UTF-8
    $pdo->exec("SET NAMES 'utf8'");

    // Optional: Test if the connection works by executing a simple query
    // Uncomment this line if you want to verify the connection
    // $pdo->query("SELECT 1");

} catch (PDOException $e) {
    // Handle any errors related to the database connection
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>
