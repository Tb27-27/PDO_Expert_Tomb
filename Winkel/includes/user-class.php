<?php

require_once "db.php";

Class User {

    // Database connection
    public $dbConnection; 
    
    // Constructor
    public function __construct() {
        $this->dbConnection = new DB();
    }

    // Register a new user
    public function registerUser($email, $username, $passwordInput) {

        $hashedPassword = password_hash($passwordInput, PASSWORD_DEFAULT, ['cost' => 13]);
        
        $this->dbConnection->run(
            "INSERT INTO users (email, username, password) VALUES (?, ?, ?)",
            [$email, $username, $hashedPassword]
        );

        return true;
    }

    // Login user with username or email
    public function loginUser($loginInput, $passwordInput) {
        
        // Check if the input is an email or username
        $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL);
        
        if ($isEmail) {
            // Search by email
            $stmt = $this->dbConnection->run(
                "SELECT id, username, email, password FROM users WHERE email = ? LIMIT 1", 
                [$loginInput]
            );
        } else {
            // Search by username
            $stmt = $this->dbConnection->run(
                "SELECT id, username, email, password FROM users WHERE username = ? LIMIT 1", 
                [$loginInput]
            );
        }

        $fetchUser = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if user exists and password is correct
        if ($fetchUser && password_verify($passwordInput, $fetchUser['password'])) {
            // Create session for security - regenerate session ID
            session_regenerate_id(true);
            
            // Store user information in session
            $_SESSION['user_id'] = $fetchUser['id'];
            $_SESSION['username'] = $fetchUser['username'];
            $_SESSION['email'] = $fetchUser['email'];
            
            return true;
        }
        
        return false;
    }

    // Logout user
    public function logOutUser() {
        // Clear all session data
        $_SESSION = [];
        
        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destroy session
        session_destroy();
    }

    // Check if user is currently logged in
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    // Get information about currently logged in user
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'] ?? '',
                'email' => $_SESSION['email'] ?? ''
            ];
        }
        return null;
    }
}

?>