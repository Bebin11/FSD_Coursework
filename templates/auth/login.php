<?php
// We override the default layout structure for login page to use the full-screen grid
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Khutta Ma Jutta</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="login-page">
    <div class="login-form-side">
        <div style="max-width: 400px; width: 100%; margin: 0 auto;">
            <div style="margin-bottom: 2rem;">
                <h1 style="font-size: 2rem; font-weight: 700; color: var(--primary);">Khutta Ma Jutta</h1>
                <p style="color: var(--text-muted); margin-top: 0.5rem;">Welcome back! Please login to your account.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div style="background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 0.5rem; margin-bottom: 1.5rem; font-size: 0.875rem;">
                    <i class="fa-solid fa-circle-exclamation"></i> <?= h($error); ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token(); ?>">
                
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-weight: 500; margin-bottom: 0.5rem;">Username</label>
                    <input type="text" name="username" style="width: 100%;" required autofocus>
                </div>

                <div style="margin-bottom: 2rem;">
                    <label style="display: block; font-weight: 500; margin-bottom: 0.5rem;">Password</label>
                    <input type="password" name="password" style="width: 100%;" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.875rem; font-size: 1rem;">
                    Sign In <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
    
    <div class="login-image-side">
        <div class="login-image-content" style="max-width: 500px;">
            <h2>Premium Footwear Inventory</h2>
            <p style="font-size: 1.125rem; opacity: 0.7; color: rgba(248, 244, 236, 0.8);">
                Manage your enterprise inventory with streamlined efficiency, real-time tracking, and advanced analytics.
            </p>
            <div style="margin-top: 3rem; font-size: 5rem; opacity: 0.1;">
                <i class="fa-solid fa-shoe-prints"></i>
            </div>
        </div>
    </div>
</div>

</body>
</html>
