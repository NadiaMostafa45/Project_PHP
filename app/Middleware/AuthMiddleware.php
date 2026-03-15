<?php
class AuthMiddleware
{
    private static function viewsUrl(string $file): string
    {
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $parts = array_values(array_filter(explode('/', $scriptName), static fn($p) => $p !== ''));

        $viewsIndex = array_search('Views', $parts, true);
        if ($viewsIndex !== false) {
            $baseParts = array_slice($parts, 0, $viewsIndex + 1);
        } else {
            $baseParts = $parts;
            if (!empty($baseParts)) {
                array_pop($baseParts);
            }
            $baseParts[] = 'Views';
        }

        $base = '/' . implode('/', $baseParts);
        return rtrim($base, '/') . '/' . ltrim($file, '/');
    }

    static function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    static function isAuthenticated(): bool
    {
        self::startSession();
        $hasValidUser = isset($_SESSION['user']) && is_array($_SESSION['user']) && !empty($_SESSION['user']);
        $hasUserId = isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] > 0;
        return $hasValidUser && $hasUserId;
    }

    static function checkAuth()
    {
        if (!self::isAuthenticated()) {
            $_SESSION = [];
            header("Location: " . self::viewsUrl('login.php'));
            exit;
        }

        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");
    }

    static function redirectByRole()
    {
        self::startSession();
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'user') {
            header("Location: " . self::viewsUrl('home.php'));
        } else {
            header("Location: " . self::viewsUrl('dashboard.php'));
        }
        exit;
    }
}
