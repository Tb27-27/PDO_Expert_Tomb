<?php

require_once "db.php";

class Product {
    
    public $dbConnection; 
    
    public function __construct() {
        $this->dbConnection = new DB();
    }

    // Nieuw product toevoegen
    public function voegProductToe($code, $omschrijving, $foto, $prijsPerStuk) {
        try {
            $sql = "INSERT INTO product (code, omschrijving, foto, prijsPerStuk) VALUES (?, ?, ?, ?)";
            $this->dbConnection->run($sql, [$code, $omschrijving, $foto, $prijsPerStuk]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Controleren of code al bestaat
    public function codeBestaatAl($code) {
        $stmt = $this->dbConnection->run("SELECT 1 FROM product WHERE code = ? LIMIT 1", [$code]);
        return $stmt->fetchColumn() !== false;
    }

    // Alle producten ophalen
    public function haalAlleProductenOp() {
        $stmt = $this->dbConnection->run("SELECT * FROM product ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Product ophalen op ID
    public function haalProductOpMetId($id) {
        $stmt = $this->dbConnection->run("SELECT * FROM product WHERE id = ? LIMIT 1", [$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Product bewerken
    public function bewerkProduct($id, $code, $omschrijving, $foto, $prijsPerStuk) {
        try {
            $sql = "UPDATE product SET code = ?, omschrijving = ?, foto = ?, prijsPerStuk = ? WHERE id = ?";
            $this->dbConnection->run($sql, [$code, $omschrijving, $foto, $prijsPerStuk, $id]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Product verwijderen
    public function verwijderProduct($id) {
        try {
            $this->dbConnection->run("DELETE FROM product WHERE id = ?", [$id]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>