<?php
// Start session to check if user is logged in
session_start();

// Simple check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDO Expert Shop - Homepage</title>
    <link rel="stylesheet" href="./css/stylesheet.css">
</head>
<body>
    <!-- Rain -->
    <div class="rain"></div>
    <div class="wisps"></div>

    <div class='user_container'>
        
        <?php if ($isLoggedIn): ?>
            <!-- Show this if user is logged in -->
            <div class='login-icon'>👋</div>
            <h1>Welkom, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p class='user_h2 subtitle-text'>
                Je bent ingelogd
            </p>
            
            <div class='action-buttons'>
                <a href="./user/dashboard-user.php" class="user_button">Dashboard</a>
                <a href="./user/logout.php" class="secondary-button">Uitloggen</a>
            </div>
            
        <?php else: ?>
            <!-- Show this if user is NOT logged in -->
            <div class='login-icon'>🛍️</div>
            <h1>PDO Expert Shop</h1>
            <p class='user_h2 subtitle-text'>
                Welkom bij de shop<br>
                <small class='undertitle'>Gemaakt door Tom Bijsterbosch - OITSDO24A</small>
            </p>
            
            <div class='divider'></div>
            
            <div class='action-buttons'>
                <a href="./user/login-user.php" class="user_button">Inloggen</a>
                <a href="./user/register-user.php" class="secondary-button">Registreren</a>
            </div>
            
        <?php endif; ?>

    </div>

    <script src="./javascript/script.js"></script>
</body>
</html>