<?php
namespace App\Models;


use Config\Database;
use PDO;

class Product {
    private $db;

    public function __construct() {
        
        $this->db = \Config\Database::getInstance()->getConnection();
    }

 
    public function getAll() {
        $query = "SELECT * FROM products";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}