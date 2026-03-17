<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';

AuthMiddleware::checkAuth();

$db = \Config\Database::getInstance()->getConnection();

$id = $_GET['id'];

$stmt = $db->prepare("
UPDATE orders
SET status='delivered'
WHERE id=?
");

$stmt->execute([$id]);

header("Location: orders.php");
exit;
