<?php
require_once '../config/Database.php';
require_once '../app/Middleware/AuthMiddleware.php';
AuthMiddleware::checkAuth();

if (($_SESSION['role'] ?? 'user') !== 'admin') {
    header('Location: home.php');
    exit;
}

$errors = [];
$old = [];

if (isset($_SESSION['add_user_errors'])) {
    $errors = (array) $_SESSION['add_user_errors'];
    $old = (array) ($_SESSION['add_user_old'] ?? []);
    unset($_SESSION['add_user_errors'], $_SESSION['add_user_old']);
}

$oldName = htmlspecialchars($old['name'] ?? '');
$oldEmail = htmlspecialchars($old['email'] ?? '');
$oldRole = $old['role'] ?? 'user';
$oldRoomNo = htmlspecialchars($old['room_no'] ?? '');
$oldExt = htmlspecialchars($old['ext'] ?? '');

$fieldErrors = is_array($errors) ? $errors : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add User | ITI Cafeteria</title>
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
    .form-control { border-radius: 14px; border: 1px solid rgba(67, 40, 24, 0.2); padding: 12px 14px; }
    .form-control:focus { border-color: var(--cappuccino); box-shadow: 0 0 0 0.2rem rgba(212, 163, 115, 0.25); }
    .quick-action { background: white; border-radius: 18px; padding: 22px; text-align: center; text-decoration: none; color: var(--espresso); display: block; border: 2px solid transparent; transition: all 0.3s; }
    .quick-action:hover { color: var(--espresso); border-color: var(--cappuccino); transform: translateY(-3px); box-shadow: 0 10px 25px rgba(67, 40, 24, 0.08); }
    .quick-action i { font-size: 1.6rem; margin-bottom: 10px; display: block; color: var(--cappuccino); }
</style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="admin-content">
    <div class="container-fluid p-4 p-md-5">
        <h2 class="page-header mb-4">Add New User</h2>

        <div class="card mb-5" style="border-radius: 22px; box-shadow: 0 8px 25px rgba(67, 40, 24, 0.06);">
            <div class="card-header" style="background: #432818; color: #f5ebe0; border-radius: 22px 22px 0 0;">
                <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Add New User</h4>
            </div>

            <div class="card-body">
                <form action="../routes/store-user.php" method="POST" enctype="multipart/form-data" novalidate>
                    
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="<?= $oldName ?>" required style="border-radius: 14px; border: 1px solid rgba(67, 40, 24, 0.2); padding: 12px 14px;">
                        <?php if (!empty($fieldErrors['name'])): ?>
                            <small class="text-danger d-block mt-1"><?= htmlspecialchars($fieldErrors['name']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= $oldEmail ?>" required style="border-radius: 14px; border: 1px solid rgba(67, 40, 24, 0.2); padding: 12px 14px;">
                        <?php if (!empty($fieldErrors['email'])): ?>
                            <small class="text-danger d-block mt-1"><?= htmlspecialchars($fieldErrors['email']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required style="border-radius: 14px; border: 1px solid rgba(67, 40, 24, 0.2); padding: 12px 14px;">
                        <?php if (!empty($fieldErrors['password'])): ?>
                            <small class="text-danger d-block mt-1"><?= htmlspecialchars($fieldErrors['password']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-control" style="border-radius: 14px; border: 1px solid rgba(67, 40, 24, 0.2); padding: 12px 14px;">
                            <option value="user" <?= $oldRole === 'user' ? 'selected' : '' ?>>User</option>
                            <option value="admin" <?= $oldRole === 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                        <?php if (!empty($fieldErrors['role'])): ?>
                            <small class="text-danger d-block mt-1"><?= htmlspecialchars($fieldErrors['role']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Room No</label>
                        <input type="number" name="room_no" class="form-control" min="1" value="<?= $oldRoomNo ?>" style="border-radius: 14px; border: 1px solid rgba(67, 40, 24, 0.2); padding: 12px 14px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ext.</label>
                        <input type="text" name="ext" class="form-control" value="<?= $oldExt ?>" style="border-radius: 14px; border: 1px solid rgba(67, 40, 24, 0.2); padding: 12px 14px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Profile Image</label>
                        <input type="file" name="image" class="form-control" style="border-radius: 14px; border: 1px solid rgba(67, 40, 24, 0.2); padding: 8px;">
                    </div>

                    <button type="submit" name="add_user" class="btn btn-primary" style="background: #432818; border-radius: 16px; font-weight: 800;">
                        Add User
                    </button>
                    <a href="users.php" class="btn btn-secondary" style="border-radius: 16px;">Back</a>
                </form>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>