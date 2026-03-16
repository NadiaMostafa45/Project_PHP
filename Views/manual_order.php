<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/Models/Product.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';

AuthMiddleware::checkAuth();

use App\Models\User;
use App\Models\Product;

$userModel = new User();
$productModel = new Product();

$users = $userModel->getAll();
$products = $productModel->getAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Manual Order</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">

<style>

:root{
--latte:#f5ebe0;
--espresso:#432818;
--cappuccino:#d4a373;
}

body{
background:var(--latte);
font-family:'Plus Jakarta Sans',sans-serif;
}

.page-header{
font-family:'Playfair Display',serif;
font-weight:900;
font-size:2rem;
}

.product-card{
background:white;
border-radius:18px;
padding:20px;
box-shadow:0 8px 25px rgba(67,40,24,0.08);
text-align:center;
transition:0.3s;
}

.product-card:hover{
transform:translateY(-5px);
}

.qty-input{
width:60px;
text-align:center;
}

.btn-brown{
background:var(--espresso);
color:white;
border:none;
border-radius:14px;
font-weight:700;
}

.btn-brown:hover{
background:var(--cappuccino);
}

</style>

</head>

<body>

<?php include 'includes/sidebar.php'; ?>

<div class="admin-content">
<div class="container-fluid p-4 p-md-5">

<h2 class="page-header mb-4">Manual Order</h2>

<form action="orders.php" method="POST">

<div class="row mb-4">

<div class="col-md-4">

<label class="fw-bold mb-2">Select User</label>

<select name="user_id" class="form-control">

<?php foreach($users as $user): ?>

<option value="<?= $user['id'] ?>">
<?= $user['name'] ?>
</option>

<?php endforeach; ?>

</select>

</div>

<div class="col-md-4">

<label class="fw-bold mb-2">Room</label>

<input type="text" name="room" class="form-control">

</div>

<div class="col-md-4">

<label class="fw-bold mb-2">Notes</label>

<input type="text" name="notes" class="form-control">

</div>

</div>

<h4 class="fw-bold mb-3">Products</h4>

<div class="row g-3">

<?php foreach($products as $product): ?>

<div class="col-md-3">

<div class="product-card">

<img src="../public/assets/images/<?= $product['img'] ?>"
style="width:80px;height:80px;object-fit:cover;border-radius:10px;margin-bottom:10px;">

<h6 class="fw-bold"><?= $product['name'] ?></h6>

<p class="text-muted"><?= $product['price'] ?> EGP</p>

<input type="number"
name="items[<?= $product['id'] ?>]"
value="0"
min="0"
class="form-control qty-input mx-auto">

</div>

</div>

<?php endforeach; ?>

</div>

<button class="btn btn-brown mt-4 px-4">
Create Order
</button>

</form>

</div>
</div>

</body>
</html>