<?php
include_once 'header.php';  // Include the header file
?>

<?php
session_start();


// Include the database connection (using PDO)
include 'db.php'; // Make sure the path is correct

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Ensure $pdo is defined before using it
if (!$pdo) {
    die("Database connection failed.");
}

// Fetch user information from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT username, email FROM users WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
    <link rel="stylesheet" href="account.css">
</head>
<body>
    
    <div class="account-container">
        <h2>My Account</h2>
        <div class="account-details">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        </div>

        <div class="account-actions">
            <a href="update_account.php" class="btn">Update Account</a>
            <a href="auth/logout.php" class="btn">Logout</a>
        </div>
    </div>
</body>
</html>
<?php
include_once 'footer.php';  // Include the footer file
?>
