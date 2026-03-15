<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/Models/Product.php';

session_start();

// Get stats
$db = \Config\Database::getInstance()->getConnection();

$productCount = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
$availableCount = $db->query("SELECT COUNT(*) FROM products WHERE available = 1")->fetchColumn();
$unavailableCount = $productCount - $availableCount;
$orderCount = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$recentOrders = $db->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | ITI Cafeteria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --latte: #f5ebe0;
            --cappuccino: #d4a373;
            --espresso: #432818;
        }

        body {
            background: #f5ebe0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--espresso);
        }

        .page-header {
            font-family: 'Playfair Display', serif;
            font-weight: 900;
            font-size: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 22px;
            padding: 28px;
            border: none;
            box-shadow: 0 8px 25px rgba(67, 40, 24, 0.06);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(67, 40, 24, 0.1);
        }

        .stat-icon {
            width: 55px;
            height: 55px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 900;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #9a8578;
        }

        .quick-action {
            background: white;
            border-radius: 18px;
            padding: 22px;
            text-align: center;
            text-decoration: none;
            color: var(--espresso);
            transition: all 0.3s;
            display: block;
            border: 2px solid transparent;
        }

        .quick-action:hover {
            color: var(--espresso);
            border-color: var(--cappuccino);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(67, 40, 24, 0.08);
        }

        .quick-action i {
            font-size: 1.6rem;
            margin-bottom: 10px;
            display: block;
            color: var(--cappuccino);
        }

        .recent-table {
            background: white;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(67, 40, 24, 0.06);
        }

        .recent-table .table {
            margin-bottom: 0;
        }

        .recent-table .table thead th {
            background: var(--espresso);
            color: var(--latte);
            font-weight: 700;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 14px 18px;
            border: none;
        }

        .recent-table .table td {
            padding: 14px 18px;
            vertical-align: middle;
            border-color: #f0e6da;
        }

        .status-pill {
            padding: 5px 12px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .status-processing {
            background: #fff3cd;
            color: #856404;
        }

        .status-delivered {
            background: #d4edda;
            color: #155724;
        }

        .status-out {
            background: #d1ecf1;
            color: #0c5460;
        }

        .welcome-banner {
            background: linear-gradient(135deg, #432818 0%, #6f4e37 100%);
            border-radius: 25px;
            padding: 35px;
            color: white;
        }

        .welcome-banner h2 {
            font-family: 'Playfair Display', serif;
            font-weight: 900;
        }
    </style>
</head>

<body>

    <?php include 'includes/sidebar.php'; ?>

    <div class="admin-content">
        <div class="container-fluid p-4 p-md-5">

            <!-- Welcome Banner -->
            <div class="welcome-banner mb-5">
                <h2 class="mb-2">Welcome back, Admin 👋</h2>
                <p class="mb-0 opacity-75">Here's what's happening with your cafeteria today.</p>
            </div>

            <!-- Stats Cards -->
            <div class="row g-4 mb-5">
                <div class="col-md-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="stat-icon" style="background: #e8f5e9; color: #2e7d32;">
                                <i class="fas fa-box-open"></i>
                            </div>
                        </div>
                        <div class="stat-number"><?= $productCount ?></div>
                        <div class="stat-label mt-1">Total Products</div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="stat-icon" style="background: #e3f2fd; color: #1565c0;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="stat-number"><?= $availableCount ?></div>
                        <div class="stat-label mt-1">Available</div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="stat-icon" style="background: #fce4ec; color: #c62828;">
                                <i class="fas fa-times-circle"></i>
                            </div>
                        </div>
                        <div class="stat-number"><?= $unavailableCount ?></div>
                        <div class="stat-label mt-1">Unavailable</div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="stat-card">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="stat-icon" style="background: #fff3e0; color: #e65100;">
                                <i class="fas fa-receipt"></i>
                            </div>
                        </div>
                        <div class="stat-number"><?= $orderCount ?></div>
                        <div class="stat-label mt-1">Total Orders</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <h5 class="fw-bold mb-3"><i class="fas fa-bolt me-2 text-warning"></i>Quick Actions</h5>
            <div class="row g-3 mb-5">
                <div class="col-6 col-md-3">
                    <a href="products.php" class="quick-action">
                        <i class="fas fa-box-open"></i>
                        <span class="fw-bold d-block">All Products</span>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="add_product.php" class="quick-action">
                        <i class="fas fa-plus-circle"></i>
                        <span class="fw-bold d-block">Add Product</span>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="users.php" class="quick-action" style="opacity: 0.5; pointer-events: none;">
                        <i class="fas fa-users"></i>
                        <span class="fw-bold d-block">All Users</span>
                        <small class="text-muted">Coming Soon</small>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="add_user.php" class="quick-action" style="opacity: 0.5; pointer-events: none;">
                        <i class="fas fa-user-plus"></i>
                        <span class="fw-bold d-block">Add User</span>
                        <small class="text-muted">Coming Soon</small>
                    </a>
                </div>
            </div>

            <!-- Recent Orders -->
            <h5 class="fw-bold mb-3"><i class="fas fa-clock me-2" style="color: var(--cappuccino);"></i>Recent Orders</h5>
            <div class="recent-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Room</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentOrders)): ?>
                            <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td class="fw-bold">#<?= $order['id'] ?></td>
                                    <td><?= date('M d, Y h:i A', strtotime($order['created_at'])) ?></td>
                                    <td><?= htmlspecialchars($order['room_no'] ?? '—') ?></td>
                                    <td class="fw-bold"><?= number_format($order['total_price'], 2) ?> EGP</td>
                                    <td>
                                        <?php
                                        $statusClass = 'status-processing';
                                        if ($order['status'] === 'delivered') $statusClass = 'status-delivered';
                                        elseif ($order['status'] === 'out for delivery') $statusClass = 'status-out';
                                        ?>
                                        <span class="status-pill <?= $statusClass ?>"><?= $order['status'] ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No orders yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>