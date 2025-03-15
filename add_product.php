<?php
session_start();
include 'db.php'; // Include the database connection

// Check if the user is an admin (you can implement a role system)
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) { // Assuming user with ID 1 is the admin
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect the form data only if it's available (avoiding undefined array key errors)
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $price = isset($_POST['price']) ? $_POST['price'] : 0;
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image = $_FILES['image']['name'];
        $target_dir = "images/"; // Make sure the images folder is writable
        $target_file = $target_dir . basename($image);

        // Check if file is an image
        if (getimagesize($_FILES['image']['tmp_name']) !== false) {
            // Move the uploaded image to the target directory
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                // Insert product into database
                $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $description, $price, $image]);

                echo "Product added successfully!";
            } else {
                $error = "There was an error uploading the image.";
            }
        } else {
            $error = "The uploaded file is not an image.";
        }
    } else {
        $error = "No image file uploaded or file error.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1>Add New Product</h1>

<?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>

<form action="add_product.php" method="POST" enctype="multipart/form-data">
    <label for="name">Product Name:</label>
    <input type="text" name="name" id="name" required><br><br>

    <label for="description">Description:</label>
    <textarea name="description" id="description" required></textarea><br><br>

    <label for="price">Price:</label>
    <input type="number" step="0.01" name="price" id="price" required><br><br>

    <label for="image">Product Image:</label>
    <input type="file" name="image" id="image" required><br><br>

    <button type="submit">Add Product</button>
</form>

</body>
</html>
