<?php
namespace App\Controllers;

use App\Models\Order;
use Config\Database;

class OrderController {
    
    public function submitOrder() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
         
            if (empty($_SESSION['cart'])) {
             
                header("Location: ../Views/home.php?error=empty_cart");
                exit;
            }

            $orderModel = new Order();
            
         
            $userId = $_SESSION['user_id'] ?? 1; 
            $notes = isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : "";
            $room = isset($_POST['room_no']) ? trim((string)$_POST['room_no']) : '';
            if ($room === '') {
                header("Location: ../Views/home.php?error=room_required");
                exit;
            }
            $cart = $_SESSION['cart'];

            $grandTotal = 0;
            $db = \Config\Database::getInstance()->getConnection();
            
           
            foreach ($cart as $id => $qty) {
                $stmt = $db->prepare("SELECT price FROM products WHERE id = ?");
                $stmt->execute([$id]);
                $product = $stmt->fetch();
                if ($product) {
                    $grandTotal += ($qty * $product['price']);
                }
            }

            try {
             
                $orderId = $orderModel->save($userId, $grandTotal, $notes, $room, $cart);
                
                if ($orderId) {
                    
                    unset($_SESSION['cart']); 
                    header("Location: ../Views/my_orders.php?status=success");
                    exit;
                }
            } catch (\Exception $e) {
              
                header("Location: ../Views/home.php?error=failed_to_save");
                exit;
            }
        }
    }
}