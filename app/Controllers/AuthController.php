<?php
require_once __DIR__ . "/../../config/Database.php";
require_once __DIR__ . "/../../app/Middleware/AuthMiddleware.php";

use Config\Database;

class AuthController
{
    function validateInput($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    function login()
    {
        $error = [];

        if (isset($_POST['login'])) {
            $email = $this->validateInput($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email)) {
                $error[] = "Email is required";
            }
            if (empty($password)) {
                $error[] = "Password is required";
            }
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error[] = "Invalid email format";
            }

            if (count($error) == 0) {
                try {
                    $db = Database::getInstance()->getConnection();
                    $stmt = $db->prepare("SELECT id, name, email, password, role, image FROM users WHERE email = ? LIMIT 1");
                    $stmt->execute([$email]);
                    $user = $stmt->fetch(\PDO::FETCH_ASSOC);

                    if ($user && password_verify($password, $user['password'])) {
                        if (session_status() === PHP_SESSION_NONE) {
                            session_start();
                        }

                        session_regenerate_id(true);

                        $sessionUser = $user;
                        unset($sessionUser['password']);

                        $_SESSION['user'] = $sessionUser;
                        $_SESSION['user_id'] = (int)$user['id'];
                        $_SESSION['role'] = $user['role'] ?? 'user';

                        AuthMiddleware::redirectByRole();
                        exit;
                    } else {
                        $error[] = "Invalid email or password.";
                    }
                } catch (\PDOException $e) {
                    $error[] = "Login failed due to a server error.";
                }
            }
        }
        return $error;
    }

    function forgetPassword()
    {
        $error = [];
        if (isset($_POST['forget_password'])) {
            $email = $this->validateInput($_POST['email'] ?? '');
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($email)) {
                $error[] = "Email is required";
            }
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error[] = "Invalid email format";
            }
            if (strlen($newPassword) < 6) {
                $error[] = "Password must be at least 6 characters.";
            }
            if ($newPassword != $confirmPassword) {
                $error[] = "Password confirmation does not match.";
            }

            if (count($error) == 0) {
                try {
                    $db = Database::getInstance()->getConnection();

                    $checkStmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
                    $checkStmt->execute([$email]);
                    $user = $checkStmt->fetch(\PDO::FETCH_ASSOC);

                    if (!$user) {
                        $error[] = "Email not found.";
                    } else {
                        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                        $updateStmt = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
                        $updateStmt->execute([$hashedPassword, $email]);

                        header("Location: login.php?message=Password updated successfully");
                        exit;
                    }
                } catch (\PDOException $e) {
                    $error[] = "Password reset failed due to a server error.";
                }
            }
        }
        return $error;
    }
}
