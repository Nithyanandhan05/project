<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$search = '';
if (isset($_POST['search'])) {
    $search = $_POST['search'];
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE ?");
    $stmt->execute(["%$search%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM products");
}
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Portal</title>
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Carousel styles */
        .carousel-container {
            position: relative;
            overflow: hidden;
            width: 100%;
            height: 300px; /* Adjust height as needed */
            margin-top: 10px;
        }
        
        .carousel {
            display: flex;
            transition: transform 0.5s ease-in-out; /* Smooth slide */
        }
        
        .carousel-item {
            min-width: 100%;
            height: 100%;
        }
        
        .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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

<div class="carousel-container">
    <div class="carousel">
        <div class="carousel-item">
            <img src="https://img-prd-pim.poorvika.com/pageimg/airpod-4-1900x600-66ece22d85248-2024.webp?width=1900&height=600&quality=75" alt="Banner 1">
        </div>
        <div class="carousel-item">
            <img src="https://img-prd-pim.poorvika.com/pageimg/Horizon-banner-web-view.webp?width=1900&height=600&quality=75" alt="Banner 2">
        </div>
        <div class="carousel-item">
            <img src="https://img-prd-pim.poorvika.com/cdn-cgi/image/width=1900,height=400,quality=75/pageimg/Nothing-Mobile-Web-banner.jpg" alt="Banner 3">
        </div>
        <div class="carousel-item">
            <img src="https://img-prd-pim.poorvika.com/cdn-cgi/image/width=1900,height=400,quality=75/pageimg/Nothing-Mobile-Web-banner.jpg" alt="Banner 4">
        </div>
        <div class="carousel-item">
            <img src="https://img-prd-pim.poorvika.com/pageimg/Laptops-Available-at-Poddorvika.webp?width=1900&height=400&quality=75" alt="Banner 5">
        </div>
    </div>
    <button class="prev" onclick="moveSlide(-1)">&#10094;</button>
    <button class="next" onclick="moveSlide(1)">&#10095;</button>
</div>

<script>
    let slideIndex = 0;

    // Function to move to the next or previous slide
    function moveSlide(n) {
        const slides = document.querySelectorAll('.carousel-item');
        slideIndex += n;
        if (slideIndex >= slides.length) {
            slideIndex = 0;
        } else if (slideIndex < 0) {
            slideIndex = slides.length - 1;
        }
        updateCarousel();
    }

    // Function to update the visible slide
    function updateCarousel() {
        const slides = document.querySelectorAll('.carousel-item');
        slides.forEach((slide, index) => {
            slide.style.display = (index === slideIndex) ? 'block' : 'none';
        });
    }

    // Automatically move to the next slide every 3 seconds
    setInterval(() => {
        moveSlide(1); // Move to the next slide
    }, 3000); // Change every 3 seconds

    // Initialize the first slide
    updateCarousel();
</script>

    <!-- Available Products Section -->
    <div class="product-section">
        <h2>Available Products</h2>
        <div class="product-container">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p>₹<?php echo htmlspecialchars($product['price']); ?></p>
                    <form method="POST" action="add_to_cart.php">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                        <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
                        <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($product['image']); ?>">
                        <button type="submit" class="btn btn-primary">Add to Cart</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>
</html>
<?php
include_once 'footer.php';  // Include the footer file
?>
