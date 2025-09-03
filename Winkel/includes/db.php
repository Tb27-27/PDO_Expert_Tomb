<?php
class DB {
    protected $pdo;
    
    // construct
    public function __construct($db = "winkel", $user="root", $pwd="", $host="localhost") {
        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pwd, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch(PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
    
    // helper method to insert into
    public function run($sql, $args = null) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($args);
            return $stmt;
        } catch(PDOException $e) {
            error_log("Query failed: " . $e->getMessage());
            throw new Exception("Query failed");
        }
    }
    
    // helper method to fetch a single row
    public function fetchOne($sql, $args = null) {
        return $this->run($sql, $args)->fetch();
    }
    
    // helper method to fetch all rows
    public function fetchAll($sql, $args = null) {
        return $this->run($sql, $args)->fetchAll();
    }
    
    // get the last inserted ID
    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}


?>