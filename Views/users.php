<?php
require_once '../config/Database.php';
require_once '../app/Models/User.php';
require_once '../app/Controllers/UserController.php';
require_once '../app/Middleware/AuthMiddleware.php';

AuthMiddleware::checkAuth();

$controller = new \App\Controllers\UserController();
$users = $controller->index();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management | ITI Cafeteria</title>
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

        .page-header {
            font-family: 'Playfair Display', serif;
            font-weight: 900;
            font-size: 2rem;
        }

        .card {
            border-radius: 22px;
            border: none;
            box-shadow: 0 8px 25px rgba(67, 40, 24, 0.06);
        }

        .card-header {
            background: var(--espresso);
            color: var(--latte);
            border-radius: 22px 22px 0 0;
            padding: 18px 24px;
        }

        .btn-primary {
            background: var(--espresso);
            border: none;
            border-radius: 16px;
            font-weight: 800;
        }

        .btn-primary:hover {
            background: var(--cappuccino);
            color: #fff;
        }

        .btn-success {
            border-radius: 16px;
            font-weight: 700;
        }

        .btn-add-user {
            background: var(--cappuccino);
            color: #fff;
            border: none;
        }

        .btn-add-user:hover {
            background: #c08d5b;
            color: #fff;
        }

        .btn-edit {
            background: #fff3cd;
            color: #856404;
            border: none;
        }

        .btn-edit:hover {
            background: #ffc107;
            color: white;
        }

        .btn-delete {
            background: #f8d7da;
            color: #721c24;
            border: none;
        }

        .btn-delete:hover {
            background: #dc3545;
            color: white;
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

        .users-table {
            background: white;
            border-radius: 22px;
            overflow: hidden;
        }

        .users-table .table thead th {
            background: #fff7ef;
            color: #5f4030;
            font-weight: 700;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
            padding: 16px 18px;
        }

        .users-table .table td {
            padding: 14px 18px;
            vertical-align: middle;
            border-color: #f0e6da;
        }

        .badge-role {
            padding: 6px 12px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .badge-admin {
            background: #efe5dd;
            color: #432818;
        }

        .badge-user {
            background: #f7f0ea;
            color: #8a6b52;
        }
    </style>
</head>

<body>

<?php include 'includes/sidebar.php'; ?>

<div class="admin-content">
    <div class="container-fluid p-4 p-md-5">

        <h2 class="page-header mb-4">All Users</h2>

        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <form method="GET" class="d-flex flex-grow-1" style="gap:10px">
                        <input type="text" name="search" class="form-control"
                               placeholder="Search users..."
                               value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        <button class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>

                    <a href="add_user.php" class="btn btn-add-user">
                        <i class="fas fa-user-plus"></i> Add User
                    </a>
                </div>
            </div>

            <div class="card-body p-0 users-table">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Room</th>
                            <th>Ext</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user): ?>
                            <tr>
                                <td>
                                    <img src="../public/assets/images/users/<?= $user['image'] ?? 'default.png' ?>"
                                         width="45"
                                         height="45"
                                         class="rounded-circle"
                                         style="object-fit:cover">
                                </td>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <span class="badge-role <?= $user['role'] === 'admin' ? 'badge-admin' : 'badge-user' ?>">
                                        <?= htmlspecialchars($user['role']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($user['room_no'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($user['ext'] ?? '—') ?></td>
                                <td>
                                    <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="../routes/user_routes.php?action=delete&id=<?= $user['id'] ?>"
                                       class="btn btn-sm btn-delete"
                                       data-bs-toggle="modal"
                                       data-bs-target="#deleteUserModal"
                                       data-user-id="<?= $user['id'] ?>"
                                       data-user-name="<?= htmlspecialchars($user['name']) ?>">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 18px;">
            <div class="modal-header" style="border-bottom: none;">
                <h5 class="modal-title" id="deleteUserLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Are you sure you want to delete <strong id="deleteUserName"></strong>?</p>
            </div>
            <div class="modal-footer" style="border-top: none;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" class="btn btn-delete" id="confirmDeleteBtn">Delete</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const deleteModal = document.getElementById('deleteUserModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const userName = button.getAttribute('data-user-name');
            const deleteLink = `../routes/user_routes.php?action=delete&id=${userId}`;

            const nameHolder = deleteModal.querySelector('#deleteUserName');
            const confirmBtn = deleteModal.querySelector('#confirmDeleteBtn');

            if (nameHolder) nameHolder.textContent = userName;
            if (confirmBtn) confirmBtn.setAttribute('href', deleteLink);
        });
    }
</script>
</body>

</html>