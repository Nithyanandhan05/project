<?php  
session_start(); 
include 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    $username = $_POST['username']; 
    $password = $_POST['password']; 

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?"); 
    $stmt->execute([$username]); 
    $user = $stmt->fetch(); 

    if ($user && password_verify($password, $user['password'])) { 
        $_SESSION['user_id'] = $user['id']; 
        header("Location: user.php"); 
        exit(); 
    } else { 
        $error_message = "Invalid username or password!"; 
    } 
} 
?>

<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Login</title> 
    <link rel="stylesheet" href="login.css"> 
</head> 
<body> 
    <div class="login-wrapper"> 
        <div class="login-box"> 
            <h1>Login</h1> 
            <form method="POST" class="login-form"> 
                <div class="input-group"> 
                    <span class="icon">&#128100;</span> <!-- User icon -->
                    <input type="text" name="username" placeholder="Username" required> 
                </div> 
                <div class="input-group"> 
                    <span class="icon">&#128274;</span> <!-- Lock icon --> 
                    <input type="password" name="password" placeholder="Password" required> 
                </div> 
                <div class="extra-options"> 
                    <label><input type="checkbox"> Remember me</label> 
                    <a href="#">Forgot password?</a> 
                </div> 
                <button type="submit" class="login-btn">Sign In</button> 
            </form> 
            <?php if (isset($error_message)): ?> 
                <p class="error-message"><?= htmlspecialchars($error_message) ?></p> 
            <?php endif; ?> 
            <p>Don’t have an account? <a href="register.php">Register</a></p> 
        </div> 
    </div> 
</body> 
</html>
