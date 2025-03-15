<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;
$discount = 0; // Placeholder discount value for now.

$search = isset($_POST['search']) ? $_POST['search'] : ''; // Initialize search variable

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>
    <link rel="stylesheet" href="cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
    /* Navigation Bar */
    .nav-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #ff6f00;
        padding: 15px 20px;
        color: white;
    }

    .nav-bar .logo {
        font-size: 24px;
        font-weight: bold;
    }

    .nav-bar .search-bar form {
        display: flex;
        align-items: center;
    }

    .nav-bar .search-bar input[type="text"] {
        padding: 5px 10px;
        border-radius: 20px 0 0 20px;
        border: none;
    }

    .nav-bar .search-bar button {
        background-color: white;
        color: #ff6f00;
        border: none;
        padding: 5px 10px;
        border-radius: 0 20px 20px 0;
        cursor: pointer;
    }

    .nav-bar .nav-items a {
        margin: 0 10px;
        color: white;
        text-decoration: none;
    }
    </style>
</head>
<body>
   <!-- Navigation Bar -->
    <header class="nav-bar">
        <div class="logo">TechSavvy</div>
        <div class="search-bar">
            <form method="POST">
                <input type="text" name="search" placeholder="Search for products..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
        <div class="nav-items">
            <a href="account.php"><i class="fas fa-user"></i> My Account</a>
            <a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
            <a href="logout.php"><i class="fas fa-logout"></i> Logout</a>
        </div>
    </header>

<div class="cart-container">
    <div class="cart-items">
        <h2>My Cart</h2>

        <?php if (empty($cart_items)): ?>
            <p>Your cart is empty.</p>
        <?php else: ?>
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <div class="cart-item-details">
                        <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                        <p>Product ID: <?php echo htmlspecialchars($item['id']); ?></p>
                        <p class="cart-item-price">₹<?php echo htmlspecialchars($item['price']); ?></p>
                        <div class="quantity-control">
                            <button class="btn btn-outline-secondary" onclick="decreaseQuantity(<?php echo $item['id']; ?>)">-</button>
                            <input type="number" value="<?php echo $item['quantity']; ?>" readonly>
                            <button class="btn btn-outline-secondary" onclick="increaseQuantity(<?php echo $item['id']; ?>)">+</button>
                        </div>
                        <a href="remove_from_cart.php?product_id=<?php echo $item['id']; ?>" class="btn-remove">Remove</a>
                    </div>
                </div>

                <?php $total += $item['price'] * $item['quantity']; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="cart-summary">
        <h3>Cart Summary</h3>
        <div class="summary-detail">
            <span>Cart Total (<?php echo count($cart_items); ?> item<?php echo (count($cart_items) > 1) ? 's' : ''; ?>)</span>
            <span>₹<?php echo number_format($total, 2); ?></span>
        </div>
        <div class="summary-detail">
            <span>Discount(s)</span>
            <span>-₹<?php echo number_format($discount, 2); ?></span>
        </div>
        <div class="summary-detail">
            <span>One Assist Charges</span>
            <span>₹0</span>
        </div>
        <div class="summary-detail total-amount">
            <span>Total Amount</span>
            <span>₹<?php echo number_format($total - $discount, 2); ?></span>
        </div>
        <form action="checkout.php" method="POST">
            <button type="submit" class="btn-continue">Continue</button>
        </form>
    </div>
</div>

<script>
    function increaseQuantity(productId) {
        // Add logic to increase product quantity in cart
    }

    function decreaseQuantity(productId) {
        // Add logic to decrease product quantity in cart
    }
</script>

</body>
</html>
