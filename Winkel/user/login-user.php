<?php
    require "../includes/user-class.php";
    
    try {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user = new User();

            // XSS Forcefield
            $email = htmlspecialchars($_POST['email']);
            $password = htmlspecialchars($_POST['password']);
        
            if($user->loginUser( $email, $password))
            {
                // FIXME: voeg hier nog wat mooie classes toe
                echo "Login gelukt, welkom" . $_SESSION['username'];
                header("refresh:5, url = ../index.php");
            }
            else
            {
                echo "Login gefaalt, voer het juiste email of wachtwoord in.";
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
    <title>Login User</title>
    <link rel="stylesheet" href="../css/stylesheet.css">
</head>
<body>
    <?php
        // <!-- FIXME: nog geen styling -->
        if (!isset($_SESSION["username"])) {  
            echo "
                <div class='user_container'>
                    <h3 class='user_h3'>Login</h3>
                    <form method='POST' class='user_form'>
                        <input type='email' name='email' placeholder='Email' required>
                        <input type='password' name='password' placeholder='Password' required>
                        <input class='user_button' type='submit'>
                    </form>
                </div>
                ";
        }
        else
        {
            echo
                "<h2 class='user_h2'>Login gelukt, welkom, " . $_SESSION['username'] . "</h2>". "<br> <a class='user_button' href='./logout.php'>log uit</a>";
        }
    ?>
</body>
</html>