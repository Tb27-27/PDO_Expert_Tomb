<?php
    require "../includes/user-class.php";

    try {   
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = new User();

            // XSS Forcefield
            $email = htmlspecialchars($_POST['email']);
            $username = htmlspecialchars($_POST['username']);
            $password = htmlspecialchars($_POST['password']);
        
            $user->registerUser($username, $email, $password);

            // FIXME: voeg hier nog wat mooie classes toe
            echo "Registratie gelukt";
            header("refresh:10, url = ../index.php");
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
</head>
<body>
    <!-- FIXME: nog geen styling -->
    <h3>Account aanmaken</h3>
    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit">
    </form>
</body>
</html>