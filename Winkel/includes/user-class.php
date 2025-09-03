<?php

require "./db.php";

Class User {

    // make database connection
    private $dbConnection; 
    
    // construct
    public function __construct() {
        $this->dbConnection = new DB();
    }

    // register user
    public function registerUser($email, $username, $passwordInput) {

        $hashedPassword = password_hash($passwordInput, PASSWORD_DEFAULT, ['cost' => 13]);
        
        $stmt = $this->dbConnection->run(
            "INSERT INTO users (email, username, password) VALUES (?, ?, ?)",
            [$email, $username, $hashedPassword]
        );
        
        // check if sucessful more than 0 returns true
        return $stmt->rowCount() > 0;

    }

    // login
    public function loginUser($email, $passwordInput) {

        // fetch user
        $user = $this->dbConnection->fetch(
            "SELECT * FROM users WHERE email = ?", 
            [$email],
            false
        );
        
        // check user and password
        if ($user && password_verify($passwordInput, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            return true;
        }
        
        return false;
    }


    // logout
    public function logOutUser() {
        session_start();
        session_destroy();
    }
}

?>