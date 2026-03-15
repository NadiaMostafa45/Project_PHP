<?php

$stmt = $db->prepare("SELECT p.name, p.price, p.img, oi.quantity 
                      FROM order_items oi 
                      JOIN products p ON oi.product_id = p.id 
                      WHERE oi.order_id = ?");
$stmt->execute([$order['id']]);
$details = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row row-cols-2 row-cols-md-4 row-cols-lg-5 g-4 py-3">
    <?php foreach ($details as $item): ?>
    <div class="col">
        <div class="product-detail-card position-relative p-3 shadow-sm text-center border-0" 
             style="background: #fff; border-radius: 25px;">
            
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" 
                  style="background: #d4a373; color: #fff; font-size: 10px; padding: 6px 12px; z-index: 10;">
                <?= number_format($item['price'], 2) ?> LE
            </span>
            
            <div class="img-container mb-3" style="background: #f8f9fa; border-radius: 20px; overflow: hidden; height: 120px;">
                <img src="../public/assets/images/<?= $item['img'] ?>" 
                     onerror="this.src='../public/assets/images/coffee.png'"
                     class="img-fluid w-100 h-100" style="object-fit: cover;">
            </div>
            
            <h6 class="fw-bold mb-1 text-dark"><?= $item['name'] ?></h6>
            <p class="text-muted small mb-0 fw-bold">Qty: <?= $item['quantity'] ?></p>
        </div>
    </div>
    <?php endforeach; ?>
</div>