<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/Controllers/UserController.php';

$controller = new \App\Controllers\UserController();
$controller->store();