<?php
require_once __DIR__ . '/../../config/Database.php';
session_start();

if (isset($_GET['id'])) {
    $order_id = $_GET['id'];
    $user_id = $_SESSION['user_id'] ?? 1;

    try {
        $db = \Config\Database::getInstance()->getConnection();
        
   
        $stmt = $db->prepare("DELETE FROM orders WHERE id = ? AND user_id = ? AND status = 'processing'");
        $stmt->execute([$order_id, $user_id]);

        header("Location: ../../Views/my_orders.php?status=success");
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
exit;