<?php

namespace App\Models;

use Config\Database;
use PDO;

class Product
{
    private $db;

    public function __construct()
    {
        $this->db = \Config\Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $query = "SELECT * FROM products ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($name, $price, $img)
    {
        $stmt = $this->db->prepare("INSERT INTO products (name, price, img, available) VALUES (?, ?, ?, 1)");
        $stmt->execute([$name, $price, $img]);
        return $this->db->lastInsertId();
    }

    public function update($id, $name, $price, $available, $img = null)
    {
        if ($img) {
            $stmt = $this->db->prepare("UPDATE products SET name = ?, price = ?, available = ?, img = ? WHERE id = ?");
            $stmt->execute([$name, $price, $available, $img, $id]);
        } else {
            $stmt = $this->db->prepare("UPDATE products SET name = ?, price = ?, available = ? WHERE id = ?");
            $stmt->execute([$name, $price, $available, $id]);
        }
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function toggleAvailability($id)
    {
        $stmt = $this->db->prepare("UPDATE products SET available = NOT available WHERE id = ?");
        $stmt->execute([$id]);
    }
}
