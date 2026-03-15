<?php
require_once __DIR__ . '/app/Middleware/AuthMiddleware.php';

if (AuthMiddleware::isAuthenticated()) {
    AuthMiddleware::redirectByRole();
}

header('Location: Views/login.php');
exit;
