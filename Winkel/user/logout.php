<?php
    require "../includes/user-class.php";

    try {   
        $user = new User();

        $user->logOutUser();
        
        echo "<h3 class='user_h3'>log out gelukt</h3>";
        header("refresh:5, url = ./login-user.php");
    } catch (Exception $e) {

        // FIXME: niet heel mooi voor de gebruiker
        echo $e->getMessage();
    }

?>