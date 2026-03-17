<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/Models/Order.php';
require_once __DIR__ . '/../app/Controllers/OrderController.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';

use App\Controllers\OrderController;

AuthMiddleware::checkAuth();

$controller = new OrderController();
$controller->submitOrder();



