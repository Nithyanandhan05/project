<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Step 1: Fetch all cart items for the user
$stmt = $pdo->prepare("SELECT cart.product_id, cart.quantity, products.price 
                       FROM cart 
                       JOIN products ON cart.product_id = products.id 
                       WHERE cart.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

if (count($cart_items) === 0) {
    echo "Your cart is empty!";
    exit();
}

// Step 2: Calculate total amount
$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

// Step 3: Insert a new order
$stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'Pending')");
$stmt->execute([$user_id, $total_amount]);
$order_id = $pdo->lastInsertId();

// Step 4: Insert each cart item into order_items table
foreach ($cart_items as $item) {
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) 
                           VALUES (?, ?, ?, ?)");
    $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
}

// Step 5: Clear the cart for the user
$stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);

// Redirect to order confirmation page or display a success message
echo "Order placed successfully!";
header("Location: order_confirmation.php?order_id=" . $order_id);
exit();
?>
