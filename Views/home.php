<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/Models/Product.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';


AuthMiddleware::checkAuth();

$displayName = 'Guest';
if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
    if (!empty($_SESSION['user']['name'])) {
        $displayName = $_SESSION['user']['name'];
    } elseif (!empty($_SESSION['user']['username'])) {
        $displayName = $_SESSION['user']['username'];
    } elseif (!empty($_SESSION['user']['email'])) {
        $displayName = explode('@', $_SESSION['user']['email'])[0];
    }
}

try {
    $productModel = new \App\Models\Product();
    $allProductsFromDB = $productModel->getAll();
} catch (Exception $e) {
    die("Database Connection Error: " . $e->getMessage());
}


$products = [];
if ($allProductsFromDB) {
    foreach ($allProductsFromDB as $p) {
        $products[$p['id']] = [
            'name'  => $p['name'],
            'price' => $p['price'],
            'img'   => $p['img']
        ];
    }
}


if (isset($_GET['action'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] == 'add' || $_GET['action'] == 'increase') {
        $_SESSION['cart'][$id] = isset($_SESSION['cart'][$id]) ? $_SESSION['cart'][$id] + 1 : 1;
    } elseif ($_GET['action'] == 'decrease') {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]--;
            if ($_SESSION['cart'][$id] <= 0) unset($_SESSION['cart'][$id]);
        }
    } elseif ($_GET['action'] == 'clear') {
        unset($_SESSION['cart']);
    }
    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITI Cafeteria | Home Luxury</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --latte: #f5ebe0;
            --cappuccino: #d4a373;
            --espresso: #432818;
            --cream: #fefae0;
            --glass: rgba(255, 255, 255, 0.7);
        }

        body {
            background: linear-gradient(rgba(245, 235, 224, 0.8), rgba(245, 235, 224, 0.8)),
                url('https://media.istockphoto.com/id/1414190213/photo/3d-rendering-of-a-luxurious-restaurant-interior.webp?a=1&b=1&s=612x612&w=0&k=20&c=Yr7_xMErvHrqnr4FZSuISUuA5DJ121DZDWcWQaN0Wvg=');
            background-size: cover;
            background-attachment: fixed;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--espresso);
        }

        .navbar {
            background: var(--glass);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            padding: 20px 0;
            margin-bottom: 40px;
        }

        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 900;
            color: var(--espresso) !important;
            font-size: 1.6rem;
        }

        .btn-logout {
            background: var(--espresso);
            color: var(--latte) !important;
            border-radius: 12px;
            padding: 8px 14px !important;
            font-weight: 700;
            text-decoration: none;
            margin-left: 14px;
        }

        .btn-logout:hover {
            background: #2e1a10;
            color: #fff !important;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-weight: 900;
            font-size: 2.5rem;
            margin-bottom: 5px;
        }

        .product-card {
            background: var(--glass);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 40px;
            padding: 30px;
            transition: all 0.5s ease;
            position: relative;
            overflow: hidden;
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .product-card:hover {
            transform: translateY(-15px) rotate(2deg);
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 30px 60px rgba(67, 40, 24, 0.15);
        }

        .img-wrapper {
            background: white;
            border-radius: 30px;
            margin-bottom: 25px;
            height: 200px;
            width: 100%;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .product-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: 0.5s;
        }

        .product-card:hover img {
            transform: scale(1.1);
        }

        .price-tag {
            font-weight: 800;
            color: var(--cappuccino);
            font-size: 1.4rem;
            display: block;
            margin-bottom: 20px;
        }

        .btn-add {
            background: var(--espresso);
            color: white;
            border-radius: 18px;
            padding: 15px;
            font-weight: 700;
            border: none;
            width: 100%;
            transition: 0.3s;
            font-size: 1.1rem;
        }

        .cart-panel {
            background: var(--espresso);
            color: var(--latte);
            border-radius: 45px;
            padding: 35px;
            position: sticky;
            top: 110px;
        }

        .cart-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 15px;
            margin-bottom: 12px;
        }

        .qty-box {
            background: var(--cappuccino);
            border-radius: 12px;
            padding: 4px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .qty-box a {
            color: var(--espresso);
            text-decoration: none;
            font-weight: 900;
        }

        .custom-input {
            background: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: white !important;
            border-radius: 15px !important;
        }


        .custom-input::placeholder {
            color: var(--latte) !important;
            opacity: 0.7;
            font-style: italic;
        }


        .deliver-label {
            color: var(--latte) !important;
            font-weight: 800 !important;
            margin-bottom: 10px;
            display: block;
        }

        .select-wrapper {
            position: relative;
            width: 100%;
        }

        .room-dropdown {
            background-color: var(--latte) !important;
            color: var(--espresso) !important;
            border: 2px solid var(--cappuccino);
            border-radius: 18px;
            padding: 12px 20px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: 0.3s;
        }

        .room-dropdown:hover {
            background-color: white !important;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .room-options {
            position: absolute;
            top: 110%;
            left: 0;
            right: 0;
            background: var(--latte);
            border-radius: 20px;
            overflow: hidden;
            display: none;
            z-index: 1000;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
            border: 1px solid var(--cappuccino);
        }

        .room-option {
            padding: 14px 20px;
            color: var(--espresso);
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
            border-bottom: 1px solid rgba(67, 40, 24, 0.05);
        }

        .room-option:hover {
            background-color: var(--espresso);
            color: var(--cappuccino) !important;
            padding-left: 25px;
        }

        .room-options.show {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .total-box {
            background: linear-gradient(135deg, #d4a373, #a67c52);
            border-radius: 25px;
            padding: 25px;
            text-align: center;
            margin: 20px 0;
            color: var(--espresso);
        }

        .order-now-btn {
            background: var(--latte);
            color: var(--espresso);
            border: none;
            border-radius: 20px;
            padding: 20px;
            font-weight: 800;
            width: 100%;
            transition: 0.3s;
        }

        .order-now-btn:hover {
            background: white;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-mug-hot me-2"></i>ITI CAFETERIA</a>
            <div class="navbar-nav ms-auto align-items-center">
                <a class="nav-link px-3 fw-bold" href="home.php">Home</a>
                <a class="nav-link px-3" href="my_orders.php">My Orders</a>
                <div class="ms-4 bg-white px-3 py-1 rounded-pill shadow-sm">
                    <span class="small text-muted me-2">Hi,</span><span class="fw-800"><?= htmlspecialchars($displayName) ?></span>
                </div>
                <a class="btn-logout" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="mb-5">
                    <h1 class="section-title">Morning Brews</h1>
                    <p class="text-muted fs-5">Crafted with passion, served with love.</p>
                </div>

                <div class="row row-cols-1 row-cols-md-3 g-5">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $id => $p): ?>
                            <div class="col">
                                <div class="product-card">
                                    <div class="img-wrapper">
                                        <img src="../public/assets/images/<?= $p['img'] ?>" alt="<?= $p['name'] ?>">
                                    </div>
                                    <h4 class="fw-bold mb-2"><?= $p['name'] ?></h4>
                                    <span class="price-tag"><?= $p['price'] ?> EGP</span>
                                    <a href="?action=add&id=<?= $id ?>" class="btn btn-add">
                                        <i class="fas fa-plus me-2"></i>Add to Tray
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center">
                            <p class="alert alert-warning">No products found in the menu.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="cart-panel shadow-lg">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3 border-secondary">
                        <h3 class="mb-0 fw-bold">My Tray</h3>
                        <?php if (!empty($_SESSION['cart'])): ?>
                            <a href="?action=clear" class="text-light small text-decoration-none opacity-75">Empty Tray</a>
                        <?php endif; ?>
                    </div>

                    <form action="../routes/process_order.php" method="POST">
                        <div class="items-list" style="max-height: 350px; overflow-y: auto;">
                            <?php
                            $total = 0;
                            if (!empty($_SESSION['cart'])):
                                foreach ($_SESSION['cart'] as $id => $qty):
                                    if (isset($products[$id])):
                                        $sub = $products[$id]['price'] * $qty;
                                        $total += $sub;
                            ?>
                                        <div class="cart-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="d-block fw-bold"><?= $products[$id]['name'] ?></span>
                                                <small class="text-warning"><?= $products[$id]['price'] ?> LE</small>
                                            </div>
                                            <div class="qty-box">
                                                <a href="?action=decrease&id=<?= $id ?>">−</a>
                                                <span class="fw-bold"><?= $qty ?></span>
                                                <a href="?action=increase&id=<?= $id ?>">+</a>
                                            </div>
                                        </div>
                                <?php endif;
                                endforeach;
                            else: ?>
                                <div class="text-center py-5 opacity-25">
                                    <i class="fas fa-coffee fa-3x mb-3"></i>
                                    <p>Start adding some joy!</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mt-4">
                            <label class="small fw-bold mb-2 opacity-75">Special Request</label>
                            <textarea name="notes" class="form-control custom-input" rows="2" placeholder="Any notes for your order?"></textarea>
                        </div>

                        <div class="mt-3">
                            <label class="deliver-label">Deliver to</label>
                            <div class="select-wrapper">
                                <div class="room-dropdown" id="roomDropdown">
                                    <span id="selectedRoom">Select Room</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="room-options" id="roomOptions">
                                    <div class="room-option" data-value="2010">Room 2010</div>
                                    <div class="room-option" data-value="2011">Room 2011</div>
                                    <div class="room-option" data-value="2012">Room 2012</div>
                                </div>
                                <input type="hidden" name="room_no" id="roomInput" required>
                            </div>
                        </div>

                        <div class="total-box">
                            <span class="text-uppercase small fw-bold">Total Payment</span>
                            <h2 class="fw-900 mb-0"><?= number_format($total, 2) ?> <small>EGP</small></h2>
                        </div>

                        <button type="submit" class="order-now-btn" <?= empty($_SESSION['cart']) ? 'disabled' : '' ?>>
                            PLACE ORDER NOW <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const dropdown = document.getElementById('roomDropdown');
        const optionsList = document.getElementById('roomOptions');
        const selectedText = document.getElementById('selectedRoom');
        const hiddenInput = document.getElementById('roomInput');

        dropdown.addEventListener('click', () => {
            optionsList.classList.toggle('show');
        });

        document.querySelectorAll('.room-option').forEach(option => {
            option.addEventListener('click', function() {
                const val = this.getAttribute('data-value');
                selectedText.innerText = this.innerText;
                hiddenInput.value = val;
                optionsList.classList.remove('show');
                dropdown.style.borderColor = 'var(--cappuccino)';
            });
        });

        window.onclick = function(event) {
            if (!event.target.matches('.room-dropdown') && !event.target.matches('.room-dropdown *')) {
                optionsList.classList.remove('show');
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>