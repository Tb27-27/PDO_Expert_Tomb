<?php
session_start();
require_once "../includes/user-class.php";
require_once "../includes/product-class.php";

// Check if user is logged in - simple session protection
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    // User is not logged in, redirect to login page
    header("Location: ./login-user.php");
    exit();
}

// User is logged in, get their info from the session
$username = $_SESSION['username'];
$email = $_SESSION['email'];
$userId = $_SESSION['user_id'];

// Get some basic statistics
$product = new Product();
$producten = $product->haalAlleProductenOp();
$aantalProducten = count($producten);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo htmlspecialchars($username); ?></title>
    <link rel="stylesheet" href="../css/stylesheet.css">
</head>
<body style="overflow-y: auto; min-height: 100vh;">
    <!-- Rain -->
    <div class="rain"></div>
    <div class="wisps"></div>

    <div class='user_container' style="max-width: 600px; margin: 40px auto;">
        <div class='login-icon'>📊</div>
        <h1>Dashboard</h1>
        <p class='user_h2 subtitle-text'>
            Welkom terug, <?php echo htmlspecialchars($username); ?>!
        </p>
        
        <!-- Account information -->
        <div class="info-section">
            <p class="info-item"><strong>👤 Gebruiker:</strong> <?php echo htmlspecialchars($username); ?></p>
            <p class="info-item"><strong>📧 Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p class="info-item"><strong>📦 Producten:</strong> <?php echo $aantalProducten; ?></p>
        </div>
        
        <div class='divider'></div>
        
        <!-- Product management navigation -->
        <div class='action-buttons'>
            <a href="../product/insert-product.php" class="user_button">+ Product Toevoegen</a>
            <a href="../product/view-product.php" class="user_button">📋 Producten Bekijken</a>
        </div>
        
        <div class='divider'></div>
        
        <!-- General navigation -->
        <div class='action-buttons'>
            <a href="../frontpage.php" class="secondary-button">← Terug naar homepage</a>
            <a href="./logout.php" class="secondary-button">Uitloggen</a>
        </div>
        
    </div>

    <script src="../javascript/script.js"></script>
</body>
</html>