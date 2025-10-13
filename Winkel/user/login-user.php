<?php
session_start();
require_once "../includes/user-class.php";

// Maak een CSRF token aan als deze nog niet bestaat
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Variabelen voor het formulier
$loginInput = '';
$errors = [];
$success = false;

try {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Controleer eerst of het CSRF token geldig is
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $errors[] = "Ongeldige beveiligingstoken. Vernieuw de pagina en probeer opnieuw.";
        } else {
            $user = new User();

            // Haal de formuliergegevens op en verwijder spaties aan begin/eind
            $loginInput = trim($_POST['login'] ?? ''); // Kan gebruikersnaam of email zijn
            $password = $_POST['password'] ?? '';

            // Controleer of alle velden zijn ingevuld
            if (empty($loginInput)) {
                $errors[] = "Gebruikersnaam of email is verplicht";
            }
            if (empty($password)) {
                $errors[] = "Wachtwoord is verplicht";
            }

            // Probeer in te loggen als er geen fouten zijn
            if (empty($errors)) {
                // Controleer of het een email of gebruikersnaam is
                $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL);
                
                if ($isEmail) {
                    // Inloggen met email
                    $stmt = $user->dbConnection->run(
                        "SELECT id, username, email, password FROM users WHERE email = ? LIMIT 1", 
                        [$loginInput]
                    );
                } else {
                    // Inloggen met gebruikersnaam
                    $stmt = $user->dbConnection->run(
                        "SELECT id, username, email, password FROM users WHERE username = ? LIMIT 1", 
                        [$loginInput]
                    );
                }

                $fetchUser = $stmt->fetch(PDO::FETCH_ASSOC);

                // Controleer het wachtwoord en maak een sessie aan
                if ($fetchUser && password_verify($password, $fetchUser['password'])) {
                    // Login succesvol - maak een nieuwe sessie aan
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $fetchUser['id'];
                    $_SESSION['username'] = $fetchUser['username'];
                    $_SESSION['email'] = $fetchUser['email'];
                    $success = true;
                } else {
                    $errors[] = "Verkeerde gebruikersnaam/email of wachtwoord";
                }
            }
        }
    }
} catch (Exception $e) {
    $errors[] = "Er is een fout opgetreden: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Welkom Terug</title>
    <link rel="stylesheet" href="../css/stylesheet.css">
</head>
<body>
    <!-- Rain -->
    <div class="rain"></div>
    <div class="wisps"></div>

    <?php if (!isset($_SESSION["username"]) && !$success): ?>
        <div class='user_container'>
            <div class='login-icon'>🔐</div>
            <h1>Welkom Terug</h1>
            <h2>Inlog gegevens:</h2>
            <h2>admin@admin.com</h2>
            <h2>123</h2>
            <p class='user_h2 subtitle-text'>
                Log in om door te gaan
            </p>
            
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <strong>⚠️ Login mislukt</strong><br>
                    <?php foreach ($errors as $error): ?>
                        • <?php echo htmlspecialchars($error); ?><br>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method='POST' class='user_form'>
                <!-- Verborgen veld met CSRF token voor beveiliging tegen CSRF aanvallen -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                
                <div class="form-group">
                    <input type='text' 
                           name='login' 
                           placeholder='Gebruikersnaam of Email' 
                           value="<?php echo htmlspecialchars($loginInput); ?>"
                           required 
                           autocomplete="username">
                </div>
                
                <div class="form-group">
                    <input type='password' 
                           name='password' 
                           placeholder='Wachtwoord' 
                           required 
                           autocomplete="current-password">
                </div>
                
                <input class='user_button' type='submit' value='Inloggen'>
            </form>
            
            <div class='divider'></div>
            
            <a href="./register-user.php" class="secondary-button">
                Nog geen account? Registreren
            </a>
            
            <a href="../frontpage.php" class="back-link">
                ← Terug naar homepage
            </a>
        </div>
        
    <?php elseif ($success || isset($_SESSION["username"])): ?>
        <div class='user_container'>
            <?php if ($success): ?>
                <div class='login-icon'>✨</div>
                <h1>Welkom, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                
                <p class='user_h2 success-login-message'>
                    ✓ Je bent succesvol ingelogd
                </p>
                
                <div class='progress-bar'>
                    <div class='progress-fill'></div>
                </div>
                
                <div class='action-buttons'>
                    <a class='user_button' href='./dashboard-user.php'>Ga naar homepage</a>
                    <a class='secondary-button' href='./logout.php'>Uitloggen</a>
                </div>
                
                <script>
                    setTimeout(function() {
                        window.location.href = './dashboard-user.php';
                    }, 3000);
                </script>
                
            <?php else: ?>
                <div class='login-icon'>👋</div>
                <h1>Welkom, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                <p class='user_h2 already-logged-in'>Je bent al ingelogd</p>
                
                <div class='action-buttons'>
                    <a class='user_button' href='./dashboard-user.php'>Ga naar dashboard</a>
                    <a class='secondary-button' href='./logout.php'>Uitloggen</a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <script src="../javascript/script.js"></script>
</body>
</html>