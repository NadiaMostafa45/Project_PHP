<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/Controllers/UserController.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';

use App\Controllers\UserController;

\AuthMiddleware::startSession();

$controller = new UserController();
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->update();
        }
        break;

    default:
        header("Location: ../Views/users.php?error=Invalid action");
        exit;
}