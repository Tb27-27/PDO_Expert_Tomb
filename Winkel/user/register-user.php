<?php
session_start();
require_once "../includes/user-class.php";

// Maak een CSRF token aan als deze nog niet bestaat
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Variabelen voor het vasthouden van formuliergegevens
$email = '';
$username = '';
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
            $email = trim($_POST['email'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            // Controleer of alle velden zijn ingevuld
            if (empty($email)) {
                $errors[] = "Email is verplicht";
            }
            if (empty($username)) {
                $errors[] = "Gebruikersnaam is verplicht";
            }
            if (empty($password)) {
                $errors[] = "Wachtwoord is verplicht";
            }

            // Controleer of het emailadres een geldig formaat heeft
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Ongeldig emailadres";
            }

            // Controleer de eisen voor de gebruikersnaam
            if (!empty($username)) {
                if (strlen($username) < 3) {
                    $errors[] = "Gebruikersnaam moet minimaal 3 karakters zijn";
                }
                if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
                    $errors[] = "Gebruikersnaam mag alleen letters, cijfers, - en _ bevatten";
                }
            }

            // Controleer de sterkte van het wachtwoord
            if (!empty($password) && strlen($password) < 6) {
                $errors[] = "Wachtwoord moet minimaal 6 karakters zijn";
            }

            // Controleer of er geen dubbele accounts zijn
            if (empty($errors)) {
                // Controleer of het emailadres al bestaat
                $emailCheck = $user->dbConnection->run(
                    "SELECT 1 FROM users WHERE email = ? LIMIT 1",
                    [$email]
                );

                if ($emailCheck->fetchColumn() !== false) {
                    $errors[] = "Dit emailadres is al in gebruik";
                }

                // Controleer of de gebruikersnaam al bestaat
                $usernameCheck = $user->dbConnection->run(
                    "SELECT 1 FROM users WHERE username = ? LIMIT 1",
                    [$username]
                );

                if ($usernameCheck->fetchColumn() !== false) {
                    $errors[] = "Deze gebruikersnaam is al in gebruik";
                }
            }

            // Sla de gebruiker op als alles klopt
            if (empty($errors)) {
                if ($user->registerUser($email, $username, $password)) {
                    $success = true;
                    // Maak de formuliervelden leeg na succesvolle registratie
                    $email = '';
                    $username = '';
                } else {
                    $errors[] = "Registratie is mislukt. Probeer het opnieuw.";
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
    <title>Register User</title>
    <link rel="stylesheet" href="../css/stylesheet.css">
</head>
<body>
    <!-- Rain -->
    <div class="rain"></div>
    <div class="wisps"></div>

    <div class='user_container'>
        <h3 class='user_h3'>Account aanmaken</h3>
        
        <?php if ($success): ?>
            <div class="success-message">
                <strong>Registratie gelukt!</strong><br>
                Je wordt doorgestuurd naar de login pagina...
                <script>
                    setTimeout(function() {
                        window.location.href = './login-user.php';
                    }, 3000);
                </script>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <strong>Registratie mislukt:</strong><br>
                <?php foreach ($errors as $error): ?>
                    • <?php echo htmlspecialchars($error); ?><br>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
            <form method="POST" class='user_form'>
                <!-- Verborgen veld met CSRF token voor beveiliging tegen CSRF aanvallen -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                
                <div class="form-group">
                    <input type="email" 
                           name="email" 
                           placeholder="Email" 
                           value="<?php echo htmlspecialchars($email); ?>"
                           required>
                </div>
                
                <div class="form-group">
                    <input type="text" 
                           name="username" 
                           placeholder="Gebruikersnaam (min. 3 karakters)" 
                           value="<?php echo htmlspecialchars($username); ?>"
                           required>
                </div>
                
                <div class="form-group">
                    <input type="password" 
                           name="password" 
                           placeholder="Wachtwoord (min. 6 karakters)" 
                           required>
                </div>
                
                <input class='user_button' type="submit" value="Registreren">
            </form>
            
            <a href="./login-user.php" class="back-link">
                Al een account? Inloggen →
            </a>
        <?php endif; ?>
    </div>

    <script src="../javascript/script.js"></script>
</body>
</html>