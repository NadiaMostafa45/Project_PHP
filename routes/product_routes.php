<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/Models/Product.php';
require_once __DIR__ . '/../app/Controllers/ProductController.php';

use App\Controllers\ProductController;

session_start();

$controller = new ProductController();
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'store':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->store();
        }
        break;

    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->update();
        }
        break;

    case 'delete':
        $controller->destroy();
        break;

    case 'toggle':
        $controller->toggleAvailability();
        break;

    default:
        header("Location: ../Views/products.php?error=Invalid action");
        exit;
}
