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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>☕ My Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --espresso: #7d4a2d;
            --cappuccino: #d4a373;
            --latte: #f5ebe0;
        }

        body { background-color: var(--latte); font-family: 'Plus Jakarta Sans', sans-serif; color: var(--espresso); }
        
        .main-card { border-radius: 40px; border: none; background: rgba(255,255,255,0.7); backdrop-filter: blur(15px); box-shadow: 0 20px 40px rgba(88, 44, 44, 0.11); }
        
       
        .order-card { 
            border-radius: 25px; 
            border: 1px solid rgba(67, 40, 24, 0.11); 
            margin-bottom: 25px; 
            background: white; 
            transition: 0.3s;
            border-left: 10px solid var(--espresso) !important;
            position: relative;
        }
        .order-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(244, 144, 21, 0.05); }

        .filter-label { color: var(--espresso); font-weight: 800; font-size: 0.8rem; letter-spacing: 1px; margin-bottom: 8px; display: block; }
        
        .btn-brown { background-color: var(--espresso); color: white; border-radius: 15px; font-weight: 700; border: none; padding: 12px 30px; transition: 0.3s; }
        .btn-brown:hover { background-color: var(--cappuccino); color: white; }

        .status-pill { background: #fefae0; color: var(--cappuccino); padding: 8px 18px; border-radius: 12px; font-weight: 800; font-size: 11px; text-transform: uppercase; border: 1px solid rgba(212, 163, 115, 0.2); }
        
        .total-banner { background: linear-gradient(135deg, var(--espresso), #6f4e37); color: white; border-radius: 30px; padding: 35px; border: none; }
     
            .date-input-luxury {
                border: 2px solid #432818 !important; 
                transition: all 0.3s ease;
                background-color: #fdfaf8 !important;
            }


                .date-input-luxury:hover, 
                .date-input-luxury:focus {
                    border-color: #d4a373 !important; 
                    background-color: #ffffff !important;
                    box-shadow: 0 0 8px rgba(212, 163, 115, 0.3); 
                    outline: none;
                }
   
   </style>
</head>
<body>

<div class="container py-5">
    <div class="card main-card p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h1 class="fw-bold m-0" style="font-family: 'Playfair Display', serif;">☕ My Orders</h1>
            <a href="home.php" class="btn btn-outline-dark rounded-pill px-4 fw-bold">Menu</a>
        </div>

        <form method="GET" class="row g-3 mb-5 p-4 bg-white rounded-4 shadow-sm align-items-end">
            <div class="col-md-4 text-start">
                <label class="filter-label">DATE FROM</label>
                <input type="date" name="date_from" value="<?= $date_from ?>" 
       class="form-control date-input-luxury rounded-3 p-2">
            </div>
            <div class="col-md-4 text-start">
                <label class="filter-label">DATE TO</label>
                <input type="date" name="date_to" value="<?= $date_to ?>" 
       class="form-control date-input-luxury rounded-3 p-2">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-brown w-100 shadow-sm">APPLY FILTER</button>
            </div>
        </form>

        <div class="orders">
            <?php foreach ($orders as $order): 
                $grand_total += $order['total_price']; 
            ?>
            <div class="order-card p-4 shadow-sm">
                <div class="row align-items-center text-center text-md-start">
                    <div class="col-md-3">
                        <small class="text-muted fw-bold d-block text-uppercase mb-1" style="font-size: 10px;">Placed on</small>
                        <span class="fw-bold"><?= date('M d, Y | h:i A', strtotime($order['created_at'])) ?></span>
                    </div>
                    <div class="col-md-2">
                        <span class="status-pill"><?= $order['status'] ?></span>
                    </div>
                    <div class="col-md-3 text-md-end">
                        <small class="text-muted fw-bold d-block text-uppercase mb-1" style="font-size: 10px;">Total Bill</small>
                        <span class="fs-4 fw-bold" style="color: var( --espresso);"><?= number_format($order['total_price'], 2) ?> EGP</span>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <?php if ($order['status'] == 'processing'): ?>
                            <a href="../app/Controllers/cancel_order.php?id=<?= $order['id'] ?>" class="btn btn-danger btn-sm rounded-pill px-4 me-2 fw-bold shadow-sm border-0" onclick="return confirm('Cancel this order?')">CANCEL</a>
                        <?php endif; ?>
                        <button class="btn btn-outline-dark btn-sm rounded-pill px-4 fw-bold" onclick="toggleDetails(<?= $order['id'] ?>)">Details</button>
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
            <div>
                <h3 class="m-0 fw-bold">Total Orders Amount</h3>
               
            </div>
            <h2 class="m-0 fw-bold display-5"><?= number_format($grand_total, 2) ?> <small class="fs-4">EGP</small></h2>
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