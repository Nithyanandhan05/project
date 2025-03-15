<?php
session_start();
include 'db.php';  // Ensure your database connection is correct
require 'vendor/autoload.php';

// Set your Stripe secret key
\Stripe\Stripe::setApiKey('sk_test_51QJdLhGzYjCEYlD6shiUZewJ38Qu8b3VwGmKq41BNFsHVZeBbNezr25p1jGdp9NRpkK4v9So9a0cLykjhX64ugzc007JuwFIMD');

// Check if form was submitted and stripeToken is available
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['stripeToken'])) {
    $token = $_POST['stripeToken'];
    $total = $_POST['amount']; // Get total amount from the hidden field
    
    // Convert the total amount to cents (Stripe works with cents)
    $amount = (int)($total * 100);

    try {
        // Create the charge on Stripe
        $charge = \Stripe\Charge::create([
            'amount' => $amount,
            'currency' => 'usd',
            'description' => 'E-commerce Order',
            'source' => $token,
        ]);

        if ($charge->status == 'succeeded') {
            // Payment was successful, create order in the database
            $user_id = $_SESSION['user_id'];
            $total_amount = $total; // Amount in dollars

            // Insert order into orders table
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, payment_status) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $total_amount, 'Paid']);

            // Get the last inserted order ID
            $order_id = $pdo->lastInsertId();

            // Retrieve items from the user's cart
            $stmt = $pdo->prepare("SELECT p.id, c.quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
            $stmt->execute([$user_id]);
            $cart_items = $stmt->fetchAll();

            // Insert each cart item into the order_items table
            foreach ($cart_items as $item) {
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$order_id, $item['id'], $item['quantity']]);
            }

            // Clear the cart after placing the order
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$user_id]);

            echo "Payment successful! Your order has been placed.";
        } else {
            echo "Payment failed! Please try again.";
        }
    } catch (\Stripe\Exception\CardException $e) {
        echo "Error: " . $e->getMessage();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
