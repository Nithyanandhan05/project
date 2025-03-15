<?php
session_start();
include 'db.php';
include 'admin_header.php'; // Include the header here

// Check if user is admin (assuming user ID 1 is admin)
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header("Location: login.php");
    exit();
}

// Fetch users, products, and orders
$users = $pdo->query("SELECT * FROM users")->fetchAll();
$products = $pdo->query("SELECT * FROM products")->fetchAll();

// Fetch orders along with product details by joining orders, order_items, and products
$orders = $pdo->query("
    SELECT 
        orders.id AS order_id,
        orders.user_id,
        orders.total_amount,
        orders.payment_status,
        orders.created_at AS order_date,
        products.name AS product_name,
        order_items.quantity,
        order_items.price AS item_price
    FROM orders
    JOIN order_items ON orders.id = order_items.order_id
    JOIN products ON order_items.product_id = products.id
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="header.css"> <!-- Include the header styles -->
    <link rel="stylesheet" href="admin.css"> <!-- Include the admin-specific CSS -->
</head>
<body>

<!-- Admin Dashboard Section -->
<div class="admin-dashboard-container">
    <!-- Sidebar Section -->
    <div class="sidebar">
        <h3>Admin Panel</h3>
        <ul>
            <li><a href="javascript:void(0)" onclick="toggleSection('usersSection')">Users</a></li>
            <li><a href="javascript:void(0)" onclick="toggleSection('productsSection')">Products</a></li>
            <li><a href="javascript:void(0)" onclick="toggleSection('ordersSection')">Orders</a></li>
        </ul>
    </div>

    <!-- Main Content Section -->
    <div class="main-content">
        <!-- Users Section -->
        <div id="usersSection" class="section active">
            <h2>User Details</h2>
            <ul class="user-list">
                <?php foreach ($users as $user): ?>
                    <li>
                        <?php echo htmlspecialchars($user['username']); ?> (<?php echo htmlspecialchars($user['email']); ?>)
                        <a href="javascript:void(0)" onclick="viewUserDetails(<?php echo $user['id']; ?>)">View Details</a>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="delete_user" class="btn btn-danger">Delete</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Products Section -->
        <div id="productsSection" class="section">
            <h2>Product Management</h2>
            <form action="add_product.php" method="POST" class="add-product-form">
                <input type="text" name="product_name" placeholder="Product Name" required>
                <input type="number" name="price" placeholder="Price" required>
                <select name="category" required>
                    <option value="">Select Category</option>
                    <option value="electronics">Electronics</option>
                    <option value="clothing">Clothing</option>
                </select>
                <button type="submit" class="btn btn-primary">Add Product</button>
            </form>

            <div class="product-list">
                <h3>Existing Products</h3>
                <ul>
                    <?php foreach ($products as $product): ?>
                        <li>
                            <?php echo htmlspecialchars($product['name']); ?> - $<?php echo htmlspecialchars($product['price']); ?>
                            <a href="javascript:void(0)" onclick="viewProductDetails(<?php echo $product['id']; ?>)">View Details</a>
                            <form method="POST" action="delete_product.php" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <!-- Orders Section -->
        <div id="ordersSection" class="section">
            <h2>Order History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>User ID</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Action</th> <!-- Action for viewing details -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                            <td><?php echo htmlspecialchars($order['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                            <td>$<?php echo number_format($order['item_price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                            <td><?php echo htmlspecialchars($order['payment_status']); ?></td>
                            <td><a href="javascript:void(0)" onclick="viewOrderDetails(<?php echo $order['order_id']; ?>)">View Details</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<a href="logout.php" class="logout-btn">Logout</a>

<script src="admin.js"></script> <!-- Include your JavaScript for interactive features -->

</body>
</html>
