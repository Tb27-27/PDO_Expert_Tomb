<?php

require "../includes/db.php";

Class User {

    // make database connection
    public $dbConnection; 
    
    // construct
    public function __construct() {
        $this->dbConnection = new DB();
    }

    // register user
    public function registerUser($email, $username, $passwordInput) {

        $hashedPassword = password_hash($passwordInput, PASSWORD_DEFAULT, ['cost' => 13]);
        
        $this->dbConnection->run(
            "INSERT INTO users (email, username, password) VALUES (?, ?, ?)",
            [$email, $username, $hashedPassword]
        );

        return true;
    }

    // login
    public function loginUser($email, $passwordInput) {

        // fetch user
        $stmt = $this->dbConnection->run(
            "SELECT id, username, email, password FROM users WHERE email = ? LIMIT 1", 
            [$email]
        );

        $fetchUser = $stmt->fetch(PDO::FETCH_ASSOC);

        // check user and password
        if ($fetchUser && password_verify($passwordInput,$fetchUser['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $fetchUser['id'];
            $_SESSION['username'] = $fetchUser['username'];
            return true;
        }
        
        return false;
    }


    // logout
    public function logOutUser() {
        $_SESSION = [];
        session_destroy();
    }
}

?>