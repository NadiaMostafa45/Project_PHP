<?php
// Get the current page filename
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<style>
    .admin-sidebar {
        width: 260px;
        min-height: 100vh;
        background: #432818;
        color: #f5ebe0;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1000;
        padding: 0;
        display: flex;
        flex-direction: column;
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
    }

    .sidebar-brand {
        padding: 28px 25px;
        font-family: 'Playfair Display', serif;
        font-weight: 900;
        font-size: 1.4rem;
        color: #f5ebe0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        text-decoration: none;
        display: block;
    }

    .sidebar-brand i {
        color: #d4a373;
    }

    .sidebar-section {
        padding: 20px 18px 8px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: #d4a373;
        font-weight: 700;
    }

    .sidebar-nav {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar-nav li a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 25px;
        color: rgba(245, 235, 224, 0.7);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.92rem;
        transition: all 0.2s;
        border-left: 3px solid transparent;
    }

    .sidebar-nav li a:hover {
        background: rgba(255, 255, 255, 0.06);
        color: #f5ebe0;
        border-left-color: #d4a373;
    }

    .sidebar-nav li a.active {
        background: rgba(212, 163, 115, 0.15);
        color: #d4a373;
        border-left-color: #d4a373;
    }

    .sidebar-nav li a i {
        width: 20px;
        text-align: center;
        font-size: 1rem;
    }

    .sidebar-badge {
        background: #d4a373;
        color: #432818;
        font-size: 0.7rem;
        padding: 2px 8px;
        border-radius: 8px;
        font-weight: 800;
        margin-left: auto;
    }

    .admin-content {
        margin-left: 260px;
        min-height: 100vh;
    }

    .sidebar-footer {
        margin-top: auto;
        padding: 20px 25px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
    }

    .sidebar-footer a {
        color: rgba(245, 235, 224, 0.5);
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 600;
        transition: 0.2s;
    }

    .sidebar-footer a:hover {
        color: #f5ebe0;
    }
</style>

<aside class="admin-sidebar">
    <a href="dashboard.php" class="sidebar-brand">
        <i class="fas fa-mug-hot me-2"></i>ITI Cafeteria
    </a>

    <div class="sidebar-section">Main</div>
    <ul class="sidebar-nav">
        <li>
            <a href="dashboard.php" class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
        </li>
    </ul>

    <div class="sidebar-section">Management</div>
    <ul class="sidebar-nav">
        <li>
            <a href="users.php" class="<?= $currentPage === 'users.php' ? 'active' : '' ?>">
                <i class="fas fa-users"></i> All Users
            </a>
        </li>
        <li>
            <a href="add_user.php" class="<?= $currentPage === 'add_user.php' ? 'active' : '' ?>">
                <i class="fas fa-user-plus"></i> Add User
            </a>
        </li>
    </ul>

    <div class="sidebar-section">Products</div>
    <ul class="sidebar-nav">
        <li>
            <a href="products.php" class="<?= in_array($currentPage, ['products.php', 'edit_product.php']) ? 'active' : '' ?>">
                <i class="fas fa-box-open"></i> All Products
            </a>
        </li>
        <li>
            <a href="add_product.php" class="<?= $currentPage === 'add_product.php' ? 'active' : '' ?>">
                <i class="fas fa-plus-circle"></i> Add Product
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <a href="logout.php" class="mt-2 d-inline-block"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
    </div>
</aside>
<?php require_once __DIR__ . '/navigation_lock.php'; ?>