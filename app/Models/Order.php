<?php
namespace App\Models;

use Config\Database;
use Exception;

class Order {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

  
    public function save($userId, $total, $notes, $room, $items) {
        try {
            $this->db->beginTransaction();

       
            $query = "INSERT INTO orders (user_id, total_price, notes, room_no, status, created_at) 
                      VALUES (?, ?, ?, ?, 'processing', NOW())";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId, $total, $notes, $room]);
            $orderId = $this->db->lastInsertId();

         
            $itemQuery = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $itemStmt = $this->db->prepare($itemQuery);

            if (!empty($items) && is_array($items)) {
                foreach ($items as $productId => $qty) {
                
                    $priceStmt = $this->db->prepare("SELECT price FROM products WHERE id = ?");
                    $priceStmt->execute([$productId]);
                    $productPrice = $priceStmt->fetchColumn();

                    $itemStmt->execute([$orderId, $productId, $qty, $productPrice]);
                }
            }

            $this->db->commit();
            return $orderId; 
        } catch (Exception $e) {
            $this->db->rollBack();
            die("❌ Database Error: " . $e->getMessage()); 
        }
    }
}