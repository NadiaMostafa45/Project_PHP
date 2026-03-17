<?php
require_once __DIR__ . "/../app/Controllers/AuthController.php";
require_once __DIR__ . "/../app/Middleware/AuthMiddleware.php";

AuthMiddleware::startSession();

if (AuthMiddleware::isAuthenticated()) {
    AuthMiddleware::redirectByRole();
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $auth = new AuthController();
    $result = $auth->login();

    $errors = is_array($result) ? $result : [$result];
}

$oldEmail = htmlspecialchars($_POST['email'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Cafeteria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --latte: #f5ebe0;
            --cappuccino: #d4a373;
            --espresso: #432818;
            --cream: #fefae0;
            --glass: rgba(255, 255, 255, 0.7);
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(rgba(245, 235, 224, 0.8), rgba(245, 235, 224, 0.8)),
                url('https://media.istockphoto.com/id/1414190213/photo/3d-rendering-of-a-luxurious-restaurant-interior.webp?a=1&b=1&s=612x612&w=0&k=20&c=Yr7_xMErvHrqnr4FZSuISUuA5DJ121DZDWcWQaN0Wvg=');
            background-size: cover;
            background-attachment: fixed;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--espresso);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .login-card {
            width: 100%;
            max-width: 520px;
            background: var(--glass);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.45);
            border-radius: 32px;
            padding: 34px 30px;
            box-shadow: 0 22px 50px rgba(67, 40, 24, 0.14);
            animation: fadeUp 0.55s ease;
        }

        .brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            font-weight: 900;
            color: var(--espresso);
            margin-bottom: 6px;
        }

        .subtitle {
            color: #7e5a43;
            margin-bottom: 24px;
        }

        .form-label {
            font-weight: 700;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 14px;
            border: 1px solid rgba(67, 40, 24, 0.2);
            padding: 12px 14px;
        }

        .form-control:focus {
            border-color: var(--cappuccino);
            box-shadow: 0 0 0 0.2rem rgba(212, 163, 115, 0.25);
        }

        .btn-login {
            background: var(--espresso);
            color: #fff;
            border: 0;
            border-radius: 16px;
            padding: 13px;
            width: 100%;
            font-weight: 800;
            letter-spacing: 0.2px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-login:hover {
            background: var(--cappuccino);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(67, 40, 24, 0.2);
            color: var(--espresso);
        }

        .password-wrap {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: #7e5a43;
            cursor: pointer;
            padding: 4px;
        }

        .password-toggle:hover {
            color: var(--espresso);
        }

        .password-input {
            padding-right: 42px;
        }

        .helper-link {
            color: var(--espresso);
            font-weight: 600;
            text-decoration: none;
        }

        .helper-link:hover {
            color: #2e1a10;
            text-decoration: underline;
        }

        .alert {
            border-radius: 14px;
            border: 0;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

</head>

<body>

    <main class="login-card">
        <h1 class="brand"><i class="fas fa-mug-hot me-2"></i>ITI CAFETERIA</h1>
        <p class="subtitle mb-4">Sign in First to continue your coffee ritual.</p>

        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success" role="alert">
                <?= htmlspecialchars($_GET['message']) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors) && $errors[0] !== ""): ?>
            <ul class="alert alert-danger mb-3">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input class="form-control" type="email" name="email" value="<?= $oldEmail ?>" required>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="password-wrap">
                    <input id="passwordInput" class="form-control password-input" type="password" name="password" required>
                    <button class="password-toggle" type="button" id="passwordToggle" aria-label="Show password">
                        <i class="fa-regular fa-eye" id="passwordToggleIcon"></i>
                    </button>
                </div>
            </div>
            <button class="btn btn-login" type="submit" name="login">Login</button>
        </form>

        <div class="mt-4 d-flex justify-content-between align-items-center">
            <a class="helper-link" href="forget_password.php">Forgot password?</a>
            <a class="helper-link" href="signup.php">Sign up</a>
        </div>
    </main>

    <script>
        const passwordInput = document.getElementById('passwordInput');
        const passwordToggle = document.getElementById('passwordToggle');
        const passwordToggleIcon = document.getElementById('passwordToggleIcon');

        passwordToggle.addEventListener('click', function() {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            passwordToggle.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            passwordToggleIcon.className = isHidden ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
        });
    </script>
    <?php require_once __DIR__ . '/includes/navigation_lock.php'; ?>

</body>

</html>