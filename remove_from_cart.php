<?php
session_start();

// Check if the product_id is passed as a parameter
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Check if the cart session is set and has items
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        // Loop through cart items to find and remove the specified product
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $product_id) {
                // Remove item from cart
                unset($_SESSION['cart'][$key]);
                
                // Reindex the array to avoid gaps in keys
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                
                // Redirect to the cart page with a success message
                header("Location: cart.php?status=removed");
                exit();
            }
        }
    }
}

// Redirect to cart page if no product found
header("Location: cart.php?status=notfound");
exit();
?>
