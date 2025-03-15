<?php
session_start();
include 'db.php'; // Ensure your database connection is correct
require 'vendor/autoload.php';

// Set your Stripe secret key
\Stripe\Stripe::setApiKey('sk_test_51QJdLhGzYjCEYlD6shiUZewJ38Qu8b3VwGmKq41BNFsHVZeBbNezr25p1jGdp9NRpkK4v9So9a0cLykjhX64ugzc007JuwFIMD');

// Retrieve the total amount from the POST data, default to 0 if not set
$total = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['stripeToken']) && $total > 0) {
    $token = $_POST['stripeToken'];
    $amount = (int)($total * 100);  // Convert amount to cents (Stripe expects cents)

    try {
        // Create the charge on Stripe
        $charge = \Stripe\Charge::create([
            'amount' => $amount,
            'currency' => 'usd',
            'description' => 'E-commerce Order',
            'source' => $token,
        ]);

        if ($charge->status == 'succeeded') {
            $user_id = $_SESSION['user_id'];
            $total_amount = $total; // Amount in dollars

            // Insert order into the orders table (without storing items)
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, payment_status) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $total_amount, 'Paid']);

            // Get the last inserted order ID
            $order_id = $pdo->lastInsertId();

            // Retrieve items from the user's cart (but don't insert them into order_items)
            $stmt = $pdo->prepare("SELECT p.id, c.quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
            $stmt->execute([$user_id]);
            $cart_items = $stmt->fetchAll();

            // No insertion into order_items table, just clear the cart
            // Comment out the insertion code that was saving order items

            // Clear the cart after placing the order
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$user_id]);

            echo "Payment successful! Your order has been placed.";
        }
    } catch (\Stripe\Exception\CardException $e) {
        echo "Payment failed: " . htmlspecialchars($e->getMessage());
    } catch (Exception $e) {
        echo "Error: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link href="payment.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<?php include 'header.php'; ?> <!-- Include the header -->
<body>
    
    <div class="checkout-container">
        <h2>Complete Your Payment</h2>

        <div class="checkout-content">
            <!-- Left side: Order Summary -->
            <div class="order-summary">
                <h3>Order Summary</h3>
                
                <div class="order-items">
                    <h4>Ordered Items:</h4>
                    <table class="order-items-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Retrieve items from the user's cart
                            $stmt = $pdo->prepare("SELECT p.id, p.name, p.price, c.quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
                            $stmt->execute([$_SESSION['user_id']]);
                            $cart_items = $stmt->fetchAll();

                            if (count($cart_items) > 0):
                                foreach ($cart_items as $item):
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                    <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                </tr>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="order-total">
                    <span>Total:</span> <span>$<?php echo number_format($total, 2); ?></span>
                </div>
            </div>

            <!-- Right side: Payment Form -->
            <div class="payment-form">
                <h3>Payment Details</h3>
                <form id="payment-form" action="payment.php" method="POST">
                    <div class="form-group">
                        <label for="card-number">Card Number</label>
                        <div id="card-number">
                            <!-- A Stripe Element for Card Number will be inserted here -->
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="expiry-date">Expiration Date</label>
                        <div id="expiry-date">
                            <!-- A Stripe Element for Expiry Date will be inserted here -->
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cvc">CVC</label>
                        <div id="cvc">
                            <!-- A Stripe Element for CVC will be inserted here -->
                        </div>
                    </div>

                    <button type="submit" class="btn-continue">Proceed with Payment</button>
                    <input type="hidden" name="amount" value="<?php echo htmlspecialchars($total); ?>" />
                </form>
            </div>

        </div>
    </div>

<script src="https://js.stripe.com/v3/"></script>
<script>
    var stripe = Stripe('pk_test_51QJdLhGzYjCEYlD63RX95eqEnFli9M9anedxUGZxy74i5fU1NEqinQmIHwGzdy9SIuwtcLIZmWeO8EqEzk1XQbIl00OVNF4Zuo');
    var elements = stripe.elements();

    // Create individual elements for card number, expiry date, and CVC
    var cardNumber = elements.create('cardNumber');
    cardNumber.mount('#card-number');

    var expiryDate = elements.create('cardExpiry');
    expiryDate.mount('#expiry-date');

    var cvc = elements.create('cardCvc');
    cvc.mount('#cvc');

    var form = document.getElementById('payment-form');
    form.addEventListener('submit', function(event) {
        event.preventDefault();

        // Disable the submit button to prevent double submission
        var submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = 'Processing...';

        stripe.createToken(cardNumber).then(function(result) {
            if (result.error) {
                alert(result.error.message);
                submitButton.disabled = false;
                submitButton.innerHTML = 'Proceed with Payment';
            } else {
                // Set the Stripe token to the hidden input field
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', result.token.id);
                form.appendChild(hiddenInput);

                // Now submit the form
                form.submit();
            }
        });
    });
</script>

</body>
</html>