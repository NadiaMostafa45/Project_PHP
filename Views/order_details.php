<?php

$stmt = $db->prepare("SELECT p.name, p.price, p.img, oi.quantity 
                      FROM order_items oi 
                      JOIN products p ON oi.product_id = p.id 
                      WHERE oi.order_id = ?");
$stmt->execute([$order['id']]);
$details = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row row-cols-2 row-cols-md-5 g-3">
    <?php foreach ($details as $item): ?>
    <div class="col">
        <div class="text-center p-3 rounded-4 bg-light shadow-sm">
            <img src="../public/assets/images/<?= $item['img'] ?>" 
                 onerror="this.src='../public/assets/images/coffee.png'"
                 class="img-fluid rounded-3 mb-2" style="height:60px; object-fit:cover;">
            <p class="small fw-bold m-0 text-dark"><?= $item['name'] ?></p>
            <small class="text-muted"><?= $item['quantity'] ?> x <?= $item['price'] ?> LE</small>
        </div>
    </div>
    <?php endforeach; ?>
</div>