<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/Models/Product.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';

AuthMiddleware::checkAuth();

$productModel = new \App\Models\Product();
$error = current([$_GET['error'] ?? '']);

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: products.php?error=Invalid product ID");
    exit;
}

$product = $productModel->getById($id);
if (!$product) {
    header("Location: products.php?error=Product not found");
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product | ITI Cafeteria Admin</title>
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

        .form-card {
            background: white;
            border-radius: 25px;
            box-shadow: 0 8px 25px rgba(67, 40, 24, 0.06);
            max-width: 600px;
        }

        .form-control,
        .form-select {
            border-radius: 14px;
            padding: 14px 18px;
            border: 1px solid #e0d5c8;
            font-weight: 500;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--cappuccino);
            box-shadow: 0 0 0 3px rgba(212, 163, 115, 0.2);
        }

        .form-label {
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6f4e37;
        }

        .btn-submit {
            background: var(--espresso);
            color: white;
            border-radius: 16px;
            padding: 14px 40px;
            font-weight: 700;
            border: none;
            font-size: 1rem;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background: var(--cappuccino);
            color: var(--espresso);
        }

        .btn-back {
            background: white;
            color: var(--espresso);
            border-radius: 16px;
            padding: 14px 30px;
            font-weight: 700;
            border: 1px solid #e0d5c8;
            transition: 0.3s;
        }

        .btn-back:hover {
            background: var(--latte);
        }

        .current-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 18px;
            border: 2px solid var(--cappuccino);
        }

        .img-preview {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 20px;
            border: 3px dashed var(--cappuccino);
            display: none;
        }
    </style>
</head>

<body>

    <?php include 'includes/sidebar.php'; ?>

    <div class="admin-content">
        <div class="container-fluid p-4 p-md-5">

            <h1 class="page-header mb-4"><i class="fas fa-edit me-2"></i>Edit Product</h1>

            <div class="form-card p-4 p-md-5">

                <?php if ($error): ?>
                    <div class="alert alert-danger rounded-4">❌ <?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form action="../routes/product_routes.php?action=update&id=<?= $id ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" required
                            value="<?= htmlspecialchars($product['name']) ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Price (EGP)</label>
                        <input type="number" name="price" class="form-control" step="0.01" min="0.01" required
                            value="<?= htmlspecialchars($product['price']) ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Availability Status</label>
                        <select name="available" class="form-control">
                            <option value="1" <?= $product['available'] ? 'selected' : '' ?>>Available</option>
                            <option value="0" <?= !$product['available'] ? 'selected' : '' ?>>Unavailable</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Current Image</label>
                        <div class="mb-2">
                            <img src="../public/assets/images/<?= htmlspecialchars($product['img']) ?>"
                                onerror="this.src='../public/assets/images/coffee.png'"
                                class="current-img" alt="Current">
                        </div>
                        <label class="form-label mt-2">Upload New Image (optional)</label>
                        <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this)">
                        <img id="imgPreview" class="img-preview mt-3" alt="Preview">
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-submit flex-grow-1">
                            <i class="fas fa-save me-2"></i>Update Product
                        </button>
                        <a href="products.php" class="btn btn-back">Cancel</a>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('imgPreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide alerts after 2 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                });
            }, 2000);
        });
    </script>
</body>

</html>