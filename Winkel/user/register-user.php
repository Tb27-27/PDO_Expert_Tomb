<?php
    require "../includes/user-class.php";

    try {   
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = new User();

            // XSS Forcefield
            $username = htmlspecialchars($_POST['']);
            $email = htmlspecialchars($_POST['']);
            $password = htmlspecialchars($_POST['']);
        
            $user->registerUser($name, $email, $password);

            // voeg hier nog wat mooie classes toe
            echo "Registratie gelukt";
            header("refresh:4, url = ../index.php");
        }
    }
    catch (Exception $e) {

        // niet heel mooi voor de gebruiker
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
    <h3>Account aanmaken</h3>
    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text">
    </form>
</body>
</html>