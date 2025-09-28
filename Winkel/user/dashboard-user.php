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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User</title>
    <link rel="stylesheet" href="../css/stylesheet.css">
</head>
<body>
    <!-- Rain -->
    <div class="rain"></div>

    <div class='user_container' style="max-width: 600px;">
        <h1>Welkom op je dashboard</h1>
        <h2>Hallo, <?php echo htmlspecialchars($username); ?>!</h2>
        
        <!-- Account information -->
        <div class="info-box">
            <h3>Je account informatie:</h3>
            <p><strong>Gebruikersnaam:</strong> <?php echo htmlspecialchars($username); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>User ID:</strong> <?php echo htmlspecialchars($userId); ?></p>
        </div>
        
        <!-- Product management section -->
        <div class="info-box">
            <h3>Product Management</h3>
            <p><strong>Aantal producten in database:</strong> <?php echo $aantalProducten; ?></p>
            <p>Beheer je producten via de onderstaande opties:</p>
        </div>
        
        <!-- Product management navigation -->
        <div class="dashboard-nav">
            <a href="../product/insert-product.php" class="login_button">+ Product Toevoegen</a>
            <a href="../product/view-product.php" class="user_button">Producten Bekijken</a>
        </div>
        
        <!-- General navigation -->
        <div class="dashboard-nav">
            <a href="../frontpage.php" class="user_button">Naar homepage</a>
            <a href="./logout.php" class="user_button">Uitloggen</a>
        </div>
        
    </div>

    <script src="../javascript/script.js"></script>
</body>
</html>