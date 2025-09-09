<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/stylesheet.css">
</head>
<body>
    <?php
        require "../includes/user-class.php";

        try {   
            $user = new User();

            $user->logOutUser();
            
            echo "
            <div class='user_container'>
                <h3 class='user_h3'>log out gelukt</h3>
                </div>
                ";
            header("refresh:5, url = ./login-user.php");
        } catch (Exception $e) {

            // FIXME: niet heel mooi voor de gebruiker
            echo $e->getMessage();
        }
    ?>
</body>
</html>
