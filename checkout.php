<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Retrieve cart items from the session
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;
$discount = 0; // Placeholder for future discount functionality

foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="cart.css">
    <style>
        /* Styling similar to previous checkout page */
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; }
        .checkout-container { max-width: 800px; margin: auto; padding: 20px; background: #fff; }
        h2 { text-align: center; color: #2b8a3e; }
        .section { padding: 15px; }
        .order-summary { padding: 20px; }
        .summary-item { display: flex; justify-content: space-between; padding: 8px 0; }
        .summary-total { font-size: 18px; font-weight: bold; color: #2b8a3e; }
        .btn-proceed { width: 100%; padding: 12px; background-color: #2b8a3e; color: white; font-size: 16px; }
    </style>
</head>
<body>

<?php include 'header.php'; ?> <!-- Include the header -->

<div class="checkout-container">
    <h2>Checkout</h2>

    <!-- Section 1: Delivery Address -->
    <div class="section">
        <h3>1. Delivery Address</h3>
        <form action="process_checkout.php" method="POST">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required>
            <!-- Additional address fields as needed... -->
        </form>
    </div>

    <!-- Section 4: Order Summary -->
    <div class="section order-summary">
        <h3>Order Summary</h3>
        
        <?php if (empty($cart_items)): ?>
            <p>Your cart is empty.</p>
        <?php else: ?>
            <?php foreach ($cart_items as $item): ?>
                <div class="summary-item">
                    <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                    <span>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <div class="summary-item">
            <span>Subtotal:</span>
            <span>₹<?php echo number_format($total, 2); ?></span>
        </div>
        <div class="summary-item">
            <span>Discount:</span>
            <span>-₹<?php echo number_format($discount, 2); ?></span>
        </div>
        <div class="summary-item summary-total">
            <span>Total Amount:</span>
            <span>₹<?php echo number_format($total - $discount, 2); ?></span>
        </div>
    </div>

    <!-- Payment form -->
    <form action="payment.php" method="POST">
        <input type="hidden" name="amount" value="<?php echo $total - $discount; ?>"> <!-- Passing the total amount to payment -->
        <div class="order-summary">
            <button type="submit" class="btn-proceed">Proceed to Payment</button>
        </div>
    </form>
</div>

</body>
</html>