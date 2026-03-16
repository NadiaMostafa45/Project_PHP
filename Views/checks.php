<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';

AuthMiddleware::checkAuth();

$db = \Config\Database::getInstance()->getConnection();

$query = "
SELECT users.name, COUNT(orders.id) as orders_count, SUM(orders.total_price) as total
FROM orders
JOIN users ON users.id = orders.user_id
GROUP BY users.id
";

$checks = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f5ebe0;
font-family:'Plus Jakarta Sans',sans-serif;
}

.table-card{
background:white;
border-radius:20px;
padding:25px;
box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

thead{
background:#432818;
color:white;
}

</style>

</head>

<body>

<?php include 'includes/sidebar.php'; ?>

<div class="admin-content">
<div class="container-fluid p-5">

<h2 class="mb-4 fw-bold">Checks</h2>

<div class="table-card">

<table class="table">

<thead>
<tr>
<th>User</th>
<th>Total Orders</th>
<th>Total Amount</th>
</tr>
</thead>

<tbody>

<?php foreach($checks as $check): ?>

<tr>

<td><?= $check['name'] ?></td>
<td><?= $check['orders_count'] ?></td>
<td><?= number_format($check['total'],2) ?> EGP</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>
</div>

</body>
</html>