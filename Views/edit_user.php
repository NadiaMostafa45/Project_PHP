<?php
require_once '../config/Database.php';
require_once '../app/Models/User.php';
require_once '../app/Middleware/AuthMiddleware.php';

AuthMiddleware::checkAuth();

if (($_SESSION['role'] ?? 'user') !== 'admin') {
    header('Location: home.php');
    exit;
}

$errors = [];
$old = [];

if (isset($_SESSION['edit_user_errors'])) {
    $errors = (array) $_SESSION['edit_user_errors'];
    $old = (array) ($_SESSION['edit_user_old'] ?? []);
    unset($_SESSION['edit_user_errors'], $_SESSION['edit_user_old']);
}

$userId = intval($_GET['id'] ?? 0);
if ($userId <= 0) {
    header('Location: users.php?error=Invalid user ID');
    exit;
}

$userModel = new \App\Models\User();
$user = $userModel->getById($userId);

if (!$user) {
    header('Location: users.php?error=User not found');
    exit;
}

$oldName = htmlspecialchars($old['name'] ?? $user['name']);
$oldEmail = htmlspecialchars($old['email'] ?? $user['email']);
$oldRole = $old['role'] ?? $user['role'];
$oldRoomNo = htmlspecialchars($old['room_no'] ?? $user['room_no']);
$oldExt = htmlspecialchars($old['ext'] ?? $user['ext']);
$fieldErrors = is_array($errors) ? $errors : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User | ITI Cafeteria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --latte: #f5ebe0;
            --cappuccino: #d4a373;
            --espresso: #432818;
        }
        body {
            background: #f5ebe0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--espresso);
        }
        .page-header { font-family: 'Playfair Display', serif; font-weight: 900; font-size: 2rem; }
        .card { border-radius: 22px; padding: 28px; border: none; box-shadow: 0 8px 25px rgba(67, 40, 24, 0.06); }
        .card-header { background: var(--espresso); color: var(--latte); border-radius: 22px 22px 0 0; }
        .btn-primary { background: var(--espresso); border: none; border-radius: 16px; font-weight: 800; }
        .btn-primary:hover { background: var(--cappuccino); color: #fff; }
        .form-control, .form-select { border-radius: 14px; border: 1px solid rgba(67, 40, 24, 0.2); padding: 12px 14px; }
        .form-control:focus, .form-select:focus { border-color: var(--cappuccino); box-shadow: 0 0 0 0.2rem rgba(212, 163, 115, 0.25); }
        .btn-back { border-radius: 16px; }
        .profile-img { width: 100px; height: 100px; object-fit: cover; border-radius: 18px; border: 2px solid var(--cappuccino); }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="admin-content">
    <div class="container-fluid p-4 p-md-5">
        <h2 class="page-header mb-4">Edit User</h2>

        <div class="card mb-5">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-user-edit me-2"></i>Edit User</h4>
            </div>

            <div class="card-body">
                <form action="../routes/user_routes.php?action=update&id=<?= $userId ?>" method="POST" enctype="multipart/form-data" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="<?= $oldName ?>" required>
                        <?php if (!empty($fieldErrors['name'])): ?>
                            <small class="text-danger d-block mt-1"><?= htmlspecialchars($fieldErrors['name']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= $oldEmail ?>" required>
                        <?php if (!empty($fieldErrors['email'])): ?>
                            <small class="text-danger d-block mt-1"><?= htmlspecialchars($fieldErrors['email']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password (leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control">
                        <?php if (!empty($fieldErrors['password'])): ?>
                            <small class="text-danger d-block mt-1"><?= htmlspecialchars($fieldErrors['password']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="user" <?= $oldRole === 'user' ? 'selected' : '' ?>>User</option>
                            <option value="admin" <?= $oldRole === 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                        <?php if (!empty($fieldErrors['role'])): ?>
                            <small class="text-danger d-block mt-1"><?= htmlspecialchars($fieldErrors['role']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Room No</label>
                        <input type="number" name="room_no" class="form-control" min="1" value="<?= $oldRoomNo ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ext.</label>
                        <input type="text" name="ext" class="form-control" value="<?= $oldExt ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Current Image</label>
                        <div class="mb-2">
                            <img src="../public/assets/images/users/<?= htmlspecialchars($user['image'] ?? 'default.png') ?>" class="profile-img" alt="User">
                        </div>
                        <label class="form-label mt-2">Upload New Image (optional)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-primary flex-grow-1">Update User</button>
                        <a href="users.php" class="btn btn-secondary btn-back">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>