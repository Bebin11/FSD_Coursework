<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($title ?? 'Inventory'); ?> | Khutta Ma Jutta</title>
    
    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>👟</text></svg>">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <?php if (is_logged_in()): ?>
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="index.php" class="brand">
                    <i class="fa-solid fa-shoe-prints"></i> Khutta Ma Jutta
                </a>
            </div>
            
            <nav class="nav-links">
                <a href="index.php" class="nav-link <?= ($page ?? '') === 'dashboard' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-chart-line"></i> Dashboard
                </a>
                <a href="add.php" class="nav-link <?= ($page ?? '') === 'add' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-plus-circle"></i> Add Product
                </a>
                <?php if (is_superadmin()): ?>
                <a href="users.php" class="nav-link <?= ($page ?? '') === 'users' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-user-shield"></i> User Management
                </a>
                <?php endif; ?>
                <div style="margin-top: auto;"></div> <!-- Spacer -->
                <a href="logout.php" class="nav-link">
                    <i class="fa-solid fa-sign-out-alt"></i> Logout
                </a>
            </nav>

            <div class="user-profile">
                <div class="user-avatar">
                   <?= strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                </div>
                <div class="user-info">
                    <strong><?= h($_SESSION['username']); ?></strong>
                    <small><?= h(ucfirst($_SESSION['role'])); ?></small>
                </div>
            </div>
        </aside>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="main-content">
        <?php if (is_logged_in()): ?>
            <header class="top-bar">
                <h2 style="font-size: 1.25rem; font-weight: 600;"><?= h($title ?? 'Dashboard'); ?></h2>
            </header>
        <?php endif; ?>

        <div class="content-scroll">
            <!-- Flash Messages -->
            <?php $msg = get_flash(); if ($msg): ?>
                <div class="alert alert-<?= $msg['type']; ?>" style="margin-bottom: 1.5rem; padding: 1rem; border-radius: 0.5rem; background: var(--bg-surface); border-left: 4px solid var(--<?= $msg['type'] === 'success' ? 'success-text' : 'danger-text' ?>);">
                    <i class="fa-solid fa-info-circle"></i> <?= h($msg['message']); ?>
                </div>
            <?php endif; ?>

            <!-- Page Content -->
            <?= $content; ?>
            
            <?php if (is_logged_in()): ?>
                <footer style="margin-top: 3rem; color: var(--text-muted); font-size: 0.875rem; text-align: center;">
                    &copy; <?= date('Y'); ?> Khutta Ma Jutta Enterprise System. All rights reserved.
                </footer>
            <?php endif; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../assets/js/app.js?v=<?= time(); ?>"></script>
</body>
</html>
