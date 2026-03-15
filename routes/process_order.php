<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/Models/Order.php';
require_once __DIR__ . '/../app/Controllers/OrderController.php';

use App\Controllers\OrderController;

$controller = new OrderController();
$controller->submitOrder();