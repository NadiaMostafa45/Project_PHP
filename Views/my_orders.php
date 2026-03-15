<?php
require_once __DIR__ . '/../config/Database.php';
session_start();

$user_id = $_SESSION['user_id'] ?? 1; 
$db = \Config\Database::getInstance()->getConnection();

$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';


$query = "SELECT * FROM orders WHERE user_id = ?";
$params = [$user_id];

if (!empty($date_from) && !empty($date_to)) {
    $query .= " AND created_at BETWEEN ? AND ?"; 
    $params[] = $date_from . " 00:00:00";
    $params[] = $date_to . " 23:59:59";
}

$query .= " ORDER BY created_at DESC"; 
$stmt = $db->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$grand_total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f5ebe0; font-family: 'Plus Jakarta Sans', sans-serif; color: #432818; }
        .main-card { border-radius: 40px; border: none; background: rgba(255,255,255,0.8); backdrop-filter: blur(10px); box-shadow: 0 20px 40px rgba(0,0,0,0.05); }
        .order-card { border-radius: 25px; border: 1px solid rgba(0,0,0,0.05); margin-bottom: 20px; background: white; transition: 0.3s; }
        .btn-brown { background-color: #432818; color: white; border-radius: 12px; font-weight: 600; border: none; padding: 12px 30px; }
        .btn-brown:hover { background-color: #d4a373; }
        .status-pill { background: #fefae0; color: #d4a373; padding: 6px 15px; border-radius: 10px; font-weight: 800; font-size: 11px; text-transform: uppercase; }
        .total-banner { background: linear-gradient(135deg, #432818, #6f4e37); color: white; border-radius: 25px; padding: 30px; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="card main-card p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h1 class="fw-bold m-0" style="font-family: 'Playfair Display', serif;">☕ My Orders</h1>
            <a href="home.php" class="btn btn-outline-dark rounded-pill px-4">Menu</a>
        </div>

        <form method="GET" class="row g-3 mb-5 p-4 bg-white rounded-4 shadow-sm align-items-end">
            <div class="col-md-4 text-start">
                <label class="small fw-bold text-muted mb-2">DATE FROM</label>
                <input type="date" name="date_from" value="<?= $date_from ?>" class="form-control border-0 bg-light">
            </div>
            <div class="col-md-4 text-start">
                <label class="small fw-bold text-muted mb-2">DATE TO</label>
                <input type="date" name="date_to" value="<?= $date_to ?>" class="form-control border-0 bg-light">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-brown w-100">Apply Filter</button>
            </div>
        </form>

        <div class="orders">
            <?php foreach ($orders as $order): 
                $grand_total += $order['total_price']; 
            ?>
            <div class="order-card p-4 shadow-sm">
                <div class="row align-items-center">
                    <div class="col-md-3 text-start">
                        <small class="text-muted fw-bold d-block">Order Date</small>
                        <span class="fw-bold"><?= date('M d, Y | h:i A', strtotime($order['created_at'])) ?></span>
                    </div>
                    <div class="col-md-2">
                        <span class="status-pill"><?= $order['status'] ?></span>
                    </div>
                    <div class="col-md-3 text-md-end">
                        <small class="text-muted fw-bold d-block">Total Bill</small>
                        <span class="fs-4 fw-bold text-dark"><?= number_format($order['total_price'], 2) ?> EGP</span>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <?php if ($order['status'] == 'processing'): ?>
                            <a href="../app/Controllers/cancel_order.php?id=<?= $order['id'] ?>" class="btn btn-danger btn-sm rounded-3 px-3 me-2" onclick="return confirm('Cancel this order?')">CANCEL</a>
                        <?php endif; ?>
                        <button class="btn btn-outline-dark btn-sm rounded-3 px-3" onclick="toggleDetails(<?= $order['id'] ?>)">Details</button>
                    </div>
                </div>
                <div id="details-<?= $order['id'] ?>" style="display:none;" class="mt-4 pt-4 border-top">
                    <?php include 'order_details.php'; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($grand_total > 0): ?>
        <div class="total-banner d-flex justify-content-between align-items-center mt-5 shadow-lg">
            <h3 class="m-0 fw-bold">Total Orders Amount</h3>
            <h2 class="m-0 fw-bold display-6"><?= number_format($grand_total, 2) ?> <small class="fs-5">EGP</small></h2>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function toggleDetails(id) {
        const el = document.getElementById('details-' + id);
        el.style.display = (el.style.display === 'none') ? 'block' : 'none';
    }
</script>
</body>
</html>