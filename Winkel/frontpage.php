<?php
// Start session to check if user is logged in
session_start();

// Simple check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

// Get basic statistics if logged in
$aantalProducten = 0;
if ($isLoggedIn) {
    try {
        require_once "./includes/product-class.php";
        $product = new Product();
        $producten = $product->haalAlleProductenOp();
        $aantalProducten = count($producten);
    } catch (Exception $e) {
        // If there's an error getting products, just continue
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDO Expert Shop - Homepage</title>
    <link rel="stylesheet" href="./css/stylesheet.css">
</head>
<body>
    <!-- Rain -->
    <div class="rain"></div>
    <div class="fairies"></div>

    <div class='user_container'>
        
        <?php if ($isLoggedIn): ?>
            <!-- Show this if user is logged in -->
            <h1>Welkom terug, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <h2>PDO Expert Shop Dashboard</h2>
            
            <div class="info-box">
                <h3>Jouw sessie informatie:</h3>
                <p><strong>Gebruikersnaam:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                <p><strong>Producten in database:</strong> <?php echo $aantalProducten; ?></p>
            </div>
            
            <!-- Product Management Section -->
            <div class="info-box">
                <h3>Product Management</h3>
                <p>Beheer je winkel producten via de onderstaande opties:</p>
            </div>
            
            <!-- Navigation for logged in users -->
            <div class="dashboard-nav">
                <a href="./product/view-product.php" class="login_button">Producten Bekijken</a>
                <a href="./product/insert-product.php" class="user_button">+ Product Toevoegen</a>
            </div>
            
            <div class="dashboard-nav">
                <a href="./user/dashboard-user.php" class="user_button">Dashboard</a>
                <a href="./user/logout.php" class="user_button">Uitloggen</a>
            </div>
            
        <?php else: ?>
            <!-- Show this if user is NOT logged in -->
            <h1>Welcome to the shop made from the assignments from PDO: Expert</h1>
            <h2>Made by: Tom Bijsterbosch</h2>
            <h2>Class: OITSDO24A</h2>
            <h3>Login</h3>
            
            <!-- Navigation for guests -->
            <a href="./user/login-user.php" class="login_button">Log in</a>
            <a href="./user/register-user.php" class="user_button">Registreren</a>
            
        <?php endif; ?>

    </div>

    <script src="./javascript/script.js"></script>
</body>
</html>