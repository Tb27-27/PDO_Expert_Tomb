<?php
    require "../includes/user-class.php";

    try {   
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = new User();

            // XSS Forcefield
            $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
            $username = htmlspecialchars($_POST['username']);
            $password = $_POST['password'];
            
            $emailCheck = $user->dbConnection->run(
                "SELECT 1 FROM users WHERE email = ? LIMIT 1",
                [$email]
            );

            if ($emailCheck->fetchColumn() !== false) {
                // email already exists
                echo "<h2 class='user_h2'>Email already exists</h2>";
            }
            elseif ($user->registerUser( $email,$username, $password))
            {
                // FIXME: voeg hier nog wat mooie classes toe
                echo "<h2 class='user_h2'>Registratie gelukt</h2>";
                header("refresh:5, url = ./login-user.php");
            }
            else
            {
                echo "<h2 class='user_h2'>Registratie gefaalt</h2>";
            }
        }
    }
    catch (Exception $e) {

        // FIXME: niet heel mooi voor de gebruiker
        echo $e->getMessage();
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register User</title>
    <link rel="stylesheet" href="../css/stylesheet.css">
</head>
<body>
    <div class='user_container'>
        <!-- FIXME: nog geen styling -->
        <h3 class='user_h3'>Account aanmaken</h3>
        <form method="POST" class='user_form'>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input class='user_button' type="submit">
        </form>
    </div>
</body>
</html>