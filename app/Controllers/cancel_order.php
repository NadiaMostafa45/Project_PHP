<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';

AuthMiddleware::checkAuth();

if (isset($_GET['id'])) {
    $order_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

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