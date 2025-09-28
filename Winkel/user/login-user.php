<?php

session_start();

require_once "../includes/user-class.php";

// Variables for the form
$loginInput = '';
$errors = [];
$success = false;

try {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user = new User();

        // Get and clean the input
        $loginInput = trim($_POST['login'] ?? ''); // Can be username or email
        $password = $_POST['password'] ?? '';

        // Check if everything is filled in
        if (empty($loginInput)) {
            $errors[] = "Gebruikersnaam of email is verplicht";
        }
        if (empty($password)) {
            $errors[] = "Wachtwoord is verplicht";
        }

        // Try to login if there are no errors
        if (empty($errors)) {
            // Check if it's an email or username
            $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL);
            
            if ($isEmail) {
                // Login with email
                $stmt = $user->dbConnection->run(
                    "SELECT id, username, email, password FROM users WHERE email = ? LIMIT 1", 
                    [$loginInput]
                );
            } else {
                // Login with username
                $stmt = $user->dbConnection->run(
                    "SELECT id, username, email, password FROM users WHERE username = ? LIMIT 1", 
                    [$loginInput]
                );
            }

            $fetchUser = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check password and create session
            if ($fetchUser && password_verify($password, $fetchUser['password'])) {
                // Login successful - create session
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
} catch (Exception $e) {
    $errors[] = "Er is een fout opgetreden: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login User</title>
    <link rel="stylesheet" href="../css/stylesheet.css">
</head>
<body>
    <!-- Rain -->
    <div class="rain"></div>

    <?php if (!isset($_SESSION["username"]) && !$success): ?>
        <div class='user_container'>
            <h3 class='user_h3'>Login</h3>
            
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <strong>Login mislukt:</strong><br>
                    <?php foreach ($errors as $error): ?>
                        • <?php echo htmlspecialchars($error); ?><br>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method='POST' class='user_form'>
                <div class="form-group">
                    <input type='text' 
                           name='login' 
                           placeholder='Gebruikersnaam of Email' 
                           value="<?php echo htmlspecialchars($loginInput); ?>"
                           required>
                </div>
                
                <div class="form-group">
                    <input type='password' 
                           name='password' 
                           placeholder='Wachtwoord' 
                           required>
                </div>
                
                <input class='user_button' type='submit' value='Inloggen'>
            </form>
            
            <a href="./register-user.php" class="back-link">
                Nog geen account? Registreren →
            </a>
        </div>
        
    <?php elseif ($success || isset($_SESSION["username"])): ?>
        <div class='user_container'>
            <?php if ($success): ?>
                <div class="success-message">
                    <strong>Login gelukt!</strong><br>
                    Welkom, <?php echo htmlspecialchars($_SESSION['username']); ?>!<br>
                    Je wordt doorgestuurd naar de homepage...
                    <script>
                        setTimeout(function() {
                            window.location.href = '../frontpage.php';
                        }, 3000);
                    </script>
                </div>
            <?php else: ?>
                <h2 class='user_h2'>Welkom, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
            <?php endif; ?>
            
            <a class='user_button' href='./logout.php'>Uitloggen</a>
            <br>
            <a href="../frontpage.php" class="back-link">
                ← Terug naar homepage
            </a>
        </div>
    <?php endif; ?>

    <script src="../javascript/script.js"></script>
</body>
</html>