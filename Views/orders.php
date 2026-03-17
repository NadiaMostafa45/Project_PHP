<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/Models/Order.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';

AuthMiddleware::checkAuth();

$db = \Config\Database::getInstance()->getConnection();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $userId = $_POST['user_id'];
    $notes  = $_POST['notes'] ?? "";
    $room   = $_POST['room'] ?? null;
    $items  = $_POST['items'] ?? [];

    $grandTotal = 0;

    foreach ($items as $productId => $qty) {

        if ($qty > 0) {

            $stmt = $db->prepare("SELECT price FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch();

            if ($product) {
                $grandTotal += $qty * $product['price'];
            }
        }
    }

    if ($grandTotal > 0) {

        $orderModel = new \App\Models\Order();
        $orderModel->save($userId, $grandTotal, $notes, $room, $items);

        header("Location: orders.php");
        exit;
    }
}


$orders = $db->query("
SELECT orders.*, users.name
FROM orders
JOIN users ON users.id = orders.user_id
ORDER BY created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body {
    background: #f5ebe0;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

.card-box {
    background: white;
    border-radius: 30px;
    padding: 40px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
}

.status-pill {
    background: #fefae0;
    color: #d4a373;
    padding: 6px 14px;
    border-radius: 10px;
    font-weight: bold;
}

</style>

</head>

<body>

<?php include 'includes/sidebar.php'; ?>

<div class="admin-content container py-5">

<h1 class="mb-4">Orders</h1>

<div class="card-box">

<table class="table">

<thead>
<tr>
<th>Order Date</th>
<th>Name</th>
<th>Room</th>
<th>Ext</th>
<th>Action</th>
</tr>
</thead>

<tbody>

<?php foreach ($orders as $order): ?>

<tr>

<td><?= date('Y-m-d h:i A', strtotime($order['created_at'])) ?></td>

<td><?= $order['name'] ?></td>

<td><?= $order['room_no'] ?? '-' ?></td>

<td><?= $order['ext'] ?? '-' ?></td>

<td>

<?php if ($order['status'] == 'processing'): ?>

<a href="deliver_order.php?id=<?= $order['id'] ?>" class="btn btn-success btn-sm">
Deliver
</a>

<?php else: ?>

<span class="status-pill"><?= $order['status'] ?></span>

<?php endif; ?>

</td>

</tr>


<tr>
<td colspan="5">

<?php
$itemStmt = $db->prepare("
SELECT order_items.*, products.name, products.img
FROM order_items
JOIN products ON products.id = order_items.product_id
WHERE order_items.order_id = ?
");
$itemStmt->execute([$order['id']]);
$items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="d-flex gap-4 flex-wrap">

<?php foreach ($items as $item): ?>

<div class="text-center">

<img src="../public/assets/images/<?= $item['img'] ?>"
style="width:70px;height:70px;border-radius:10px;object-fit:cover;">

<p class="mb-1"><?= $item['name'] ?></p>

<small>x<?= $item['quantity'] ?></small>

</div>

<?php endforeach; ?>

</div>

<div class="mt-3 fw-bold text-end">
Total: <?= number_format($order['total_price'], 2) ?> EGP
</div>

</td>
</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>

</body>

</html>