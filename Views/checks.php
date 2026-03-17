<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';

AuthMiddleware::checkAuth();

$db = \Config\Database::getInstance()->getConnection();


$date_from = $_GET['date_from'] ?? '';
$date_to   = $_GET['date_to'] ?? '';
$user_id   = $_GET['user_id'] ?? '';


$users = $db->query("SELECT id,name FROM users")->fetchAll(PDO::FETCH_ASSOC);

$query = "
SELECT users.id, users.name,
COUNT(orders.id) as orders_count,
SUM(orders.total_price) as total
FROM users
LEFT JOIN orders ON users.id = orders.user_id
";

$conditions = [];
$params = [];

if (!empty($date_from) && !empty($date_to)) {
    $conditions[] = "orders.created_at BETWEEN ? AND ?";
    $params[] = $date_from . " 00:00:00";
    $params[] = $date_to . " 23:59:59";
}

if (!empty($user_id)) {
    $conditions[] = "users.id = ?";
    $params[] = $user_id;
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " GROUP BY users.id";

$stmt = $db->prepare($query);
$stmt->execute($params);
$checks = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

<!-- 🔥 FILTERS (نفس الشكل بسيط) -->
<form method="GET" class="mb-4">

<div class="row g-3">

<div class="col-md-3">
<input type="date" name="date_from" value="<?= $date_from ?>" class="form-control">
</div>

<div class="col-md-3">
<input type="date" name="date_to" value="<?= $date_to ?>" class="form-control">
</div>

<div class="col-md-3">
<select name="user_id" class="form-control">
<option value="">All Users</option>
<?php foreach($users as $u): ?>
<option value="<?= $u['id'] ?>" <?= $user_id == $u['id'] ? 'selected' : '' ?>>
<?= $u['name'] ?>
</option>
<?php endforeach; ?>
</select>
</div>

<div class="col-md-3">
<button class="btn btn-dark w-100">Filter</button>
</div>

</div>

</form>

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

<tr onclick="toggleDetails(<?= $check['id'] ?>)" style="cursor:pointer">

<td>+ <?= $check['name'] ?></td>
<td><?= $check['orders_count'] ?? 0 ?></td>
<td><?= number_format($check['total'] ?? 0,2) ?> EGP</td>

</tr>


<tr id="details-<?= $check['id'] ?>" style="display:none;">
<td colspan="3">

<?php
$orderQuery = "SELECT created_at,total_price FROM orders WHERE user_id = ?";
$orderParams = [$check['id']];

if (!empty($date_from) && !empty($date_to)) {
    $orderQuery .= " AND created_at BETWEEN ? AND ?";
    $orderParams[] = $date_from . " 00:00:00";
    $orderParams[] = $date_to . " 23:59:59";
}

$orderStmt = $db->prepare($orderQuery);
$orderStmt->execute($orderParams);
$userOrders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<table class="table">

<thead>
<tr>
<th>Order Date</th>
<th>Amount</th>
</tr>
</thead>

<tbody>

<?php foreach($userOrders as $order): ?>

<tr>
<td><?= date('Y-m-d h:i A', strtotime($order['created_at'])) ?></td>
<td><?= number_format($order['total_price'],2) ?> EGP</td>
</tr>

<?php endforeach; ?>

</tbody>

</table>

</td>
</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>
</div>

<script>
function toggleDetails(id){
let el = document.getElementById('details-'+id);
el.style.display = (el.style.display === 'none') ? 'table-row' : 'none';
}
</script>

</body>
</html>