<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/Models/Product.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';

AuthMiddleware::checkAuth();

$productModel = new \App\Models\Product();
$products = $productModel->getAll();

$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products | ITI Cafeteria Admin</title>
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

        .table-container {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(67, 40, 24, 0.06);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: var(--espresso);
            color: var(--latte);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
            padding: 16px 18px;
            border: none;
        }

        .table tbody td {
            padding: 14px 18px;
            vertical-align: middle;
            border-color: #f0e6da;
        }

        .table tbody tr {
            transition: background 0.2s;
        }

        .table tbody tr:hover {
            background: #fdf8f3;
        }

        .product-img {
            width: 55px;
            height: 55px;
            object-fit: cover;
            border-radius: 14px;
            border: 2px solid var(--latte);
        }

        .badge-available {
            background: #d4edda;
            color: #155724;
            padding: 6px 14px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.78rem;
        }

        .badge-unavailable {
            background: #f8d7da;
            color: #721c24;
            padding: 6px 14px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.78rem;
        }

        .btn-add-new {
            background: var(--espresso);
            color: white;
            border-radius: 16px;
            padding: 12px 28px;
            font-weight: 700;
            border: none;
            transition: 0.3s;
        }

        .btn-add-new:hover {
            background: var(--cappuccino);
            color: var(--espresso);
        }

        .btn-action {
            border-radius: 10px;
            padding: 6px 14px;
            font-weight: 600;
            font-size: 0.82rem;
            border: none;
            transition: 0.2s;
        }

        .btn-edit {
            background: #fff3cd;
            color: #856404;
        }

        .btn-edit:hover {
            background: #ffc107;
            color: white;
        }

        .btn-delete {
            background: #f8d7da;
            color: #721c24;
        }

        .btn-delete:hover {
            background: #dc3545;
            color: white;
        }

        .btn-toggle {
            background: #d1ecf1;
            color: #0c5460;
        }

        .btn-toggle:hover {
            background: #17a2b8;
            color: white;
        }
    </style>
</head>

<body>

    <?php include 'includes/sidebar.php'; ?>

    <div class="admin-content">
        <div class="container-fluid p-4 p-md-5">

            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <h1 class="page-header m-0"><i class="fas fa-box-open me-2"></i>All Products</h1>
                <a href="add_product.php" class="btn btn-add-new">
                    <i class="fas fa-plus me-2"></i>Add New Product
                </a>
            </div>

            <?php if ($success === 'added'): ?>
                <div class="alert alert-success rounded-4">✅ Product added successfully!</div>
            <?php elseif ($success === 'updated'): ?>
                <div class="alert alert-success rounded-4">✅ Product updated successfully!</div>
            <?php elseif ($success === 'deleted'): ?>
                <div class="alert alert-success rounded-4">✅ Product deleted successfully!</div>
            <?php elseif ($success === 'toggled'): ?>
                <div class="alert alert-info rounded-4">🔄 Product availability toggled!</div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger rounded-4">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($products)): ?>
                            <?php foreach ($products as $index => $product): ?>
                                <tr>
                                    <td class="fw-bold text-muted"><?= $index + 1 ?></td>
                                    <td>
                                        <img src="../public/assets/images/<?= htmlspecialchars($product['img']) ?>"
                                            onerror="this.src='../public/assets/images/coffee.png'"
                                            class="product-img" alt="<?= htmlspecialchars($product['name']) ?>">
                                    </td>
                                    <td class="fw-bold"><?= htmlspecialchars($product['name']) ?></td>
                                    <td class="fw-bold"><?= number_format($product['price'], 2) ?> EGP</td>
                                    <td>
                                        <?php if (!empty($product['available'])): ?>
                                            <span class="badge-available">Available</span>
                                        <?php else: ?>
                                            <span class="badge-unavailable">Unavailable</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-action btn-edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="../routes/product_routes.php?action=delete&id=<?= $product['id'] ?>"
                                                class="btn btn-action btn-delete" title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this product?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <a href="../routes/product_routes.php?action=toggle&id=<?= $product['id'] ?>"
                                                class="btn btn-action btn-toggle" title="Toggle Availability">
                                                <i class="fas fa-sync-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-box-open fa-3x mb-3 d-block opacity-25"></i>
                                    No products found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide alerts after 2 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500); // Remove from DOM after transition
                });
            }, 2000);
        });
    </script>
</body>

</html>