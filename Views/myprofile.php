<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/Middleware/AuthMiddleware.php';

AuthMiddleware::checkAuth();

$userId = (int)($_SESSION['user_id'] ?? 0);
$db = \Config\Database::getInstance()->getConnection();

$stmt = $db->prepare('SELECT name, email, image FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$userId]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    header('Location: login.php');
    exit;
}

$profileImage = !empty($profile['image']) ? $profile['image'] : 'default.png';
$profileImagePath = '../public/assets/images/users/default.png';
$candidatePath = __DIR__ . '/../public/assets/images/users/' . $profileImage;
if (file_exists($candidatePath)) {
    $profileImagePath = '../public/assets/images/users/' . $profileImage;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | ITI Cafeteria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --latte: #f5ebe0;
            --cappuccino: #d4a373;
            --espresso: #432818;
            --glass: rgba(255, 255, 255, 0.72);
        }

        body {
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(rgba(245, 235, 224, 0.85), rgba(245, 235, 224, 0.85)),
                url('https://media.istockphoto.com/id/1414190213/photo/3d-rendering-of-a-luxurious-restaurant-interior.webp?a=1&b=1&s=612x612&w=0&k=20&c=Yr7_xMErvHrqnr4FZSuISUuA5DJ121DZDWcWQaN0Wvg=');
            background-size: cover;
            background-attachment: fixed;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--espresso);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .profile-card {
            width: 100%;
            max-width: 680px;
            background: var(--glass);
            backdrop-filter: blur(12px);
            border-radius: 30px;
            padding: 32px;
            box-shadow: 0 20px 45px rgba(67, 40, 24, 0.14);
        }

        .title {
            font-family: 'Playfair Display', serif;
            font-weight: 900;
            margin-bottom: 20px;
        }

        .profile-photo {
            width: 125px;
            height: 125px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid var(--cappuccino);
            box-shadow: 0 8px 18px rgba(67, 40, 24, 0.18);
        }

        .name-text {
            font-family: 'Playfair Display', serif;
            font-weight: 900;
            font-size: 2rem;
            margin-bottom: 6px;
        }

        .email-text {
            color: #7e5a43;
            font-weight: 600;
            margin-bottom: 18px;
        }

        .info-item {
            background: #fff;
            border: 1px solid rgba(67, 40, 24, 0.08);
            border-radius: 16px;
            padding: 12px 14px;
            margin-bottom: 12px;
        }

        .info-label {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 800;
            color: #8b6a55;
            margin-bottom: 4px;
        }

        .info-value {
            font-weight: 700;
        }

        .btn-back {
            background: var(--espresso);
            color: #fff;
            border: 0;
            border-radius: 14px;
            padding: 10px 16px;
            text-decoration: none;
            font-weight: 700;
        }

        .btn-back:hover {
            background: var(--cappuccino);
            color: var(--espresso);
        }

        .btn-edit {
            background: var(--cappuccino);
            color: var(--espresso);
            border: 0;
            border-radius: 14px;
            padding: 10px 16px;
            text-decoration: none;
            font-weight: 800;
        }

        .btn-edit:hover {
            background: #c9955a;
            color: #fff;
        }
    </style>
</head>

<body>
    <div class="profile-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h2 class="title m-0"><i class="fas fa-user-circle me-2"></i>My Profile</h2>
            <a class="btn-back" href="home.php"><i class="fas fa-arrow-left me-1"></i>Back to Home</a>
        </div>

        <div class="row g-4 align-items-center">
            <div class="col-md-5 text-center">
                <img src="<?= htmlspecialchars($profileImagePath) ?>" alt="Profile Image" class="profile-photo mb-3">
                <div class="name-text"><?= htmlspecialchars($profile['name']) ?></div>
                <div class="email-text"><?= htmlspecialchars($profile['email']) ?></div>
                <a class="btn-edit" href="edit_user.php?id=<?= $userId ?>"><i class="fas fa-pen me-1"></i>Edit Profile</a>
            </div>
            <div class="col-md-7">
                <div class="info-item">
                    <div class="info-label">Full Name</div>
                    <div class="info-value"><?= htmlspecialchars($profile['name']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email Address</div>
                    <div class="info-value"><?= htmlspecialchars($profile['email']) ?></div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once __DIR__ . '/includes/navigation_lock.php'; ?>
</body>

</html>