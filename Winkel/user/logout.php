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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <link rel="stylesheet" href="../css/stylesheet.css">
</head>
<body>
    <!-- Rain -->
    <div class="rain"></div>

    <div class='user_container'>
        <?php if (isset($success)): ?>
            <div class='success-message'>
                <strong>Uitloggen gelukt</strong><br>
                Tot ziens, <?php echo htmlspecialchars($username); ?>!<br>
                Je wordt doorgestuurd naar de login pagina...
            </div>
            
            <a href='./login-user.php' class='user_button'>Direct naar login</a>
            <br>
            <a href='../frontpage.php' class='back-link'>Terug naar homepage</a>
            
            <script>
                // Redirect after 3 seconds
                setTimeout(function() {
                    window.location.href = './login-user.php';
                }, 3000);
            </script>
            
        <?php else: ?>
            <div class='error-message'>
                <strong>Fout bij uitloggen:</strong><br>
                <?php echo htmlspecialchars($error); ?>
            </div>
            
            <a href='./login-user.php' class='user_button'>Naar login</a>
        <?php endif; ?>
    </div>

    <script src="../javascript/script.js"></script>
</body>
</html>