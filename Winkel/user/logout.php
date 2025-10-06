<?php
session_start();
require_once "../includes/user-class.php";

try {   
    // Get username before logging out for goodbye message
    $username = $_SESSION['username'] ?? 'Gebruiker';
    
    // Create user object and logout
    $user = new User();
    $user->logOutUser();
    
    $success = true;
    
} catch (Exception $e) {
    $error = "Er is een fout opgetreden: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - Tot Ziens</title>
    <link rel="stylesheet" href="../css/stylesheet.css">
</head>
<body>
    <!-- Rain -->
    <div class="rain"></div>
    <div class="wisps"></div>

    <div class='user_container'>
        <?php if (isset($success)): ?>
            <div class='logout-message'>
                <div class='logout-icon'>👋</div>
                
                <h1>Tot ziens, <?php echo htmlspecialchars($username); ?>!</h1>
                
                <p class='user_h2 success-login-message'>
                    ✓ Je bent succesvol uitgelogd
                </p>
                
                <div class='progress-bar'>
                    <div class='progress-fill'></div>
                </div>
                
                <div class='action-buttons'>
                    <a href='./login-user.php' class='user_button'>Direct naar login</a>
                    <a href='../frontpage.php' class='secondary-button'>← Terug naar homepage</a>
                </div>
            </div>
            
            <script>
                // Redirect after 3 seconds
                setTimeout(() => {
                    window.location.href = './login-user.php';
                }, 3000);
            </script>
            
        <?php else: ?>
            <div class='logout-icon'>⚠️</div>
            
            <h1>Fout bij uitloggen</h1>
            
            <div class='error-message'>
                <strong>Er ging iets mis:</strong><br>
                <?php echo htmlspecialchars($error); ?>
            </div>
            
            <div class='action-buttons'>
                <a href='./login-user.php' class='user_button'>Naar login pagina</a>
                <a href='../frontpage.php' class='secondary-button'>← Terug naar homepage</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="../javascript/script.js"></script>
</body>
</html>