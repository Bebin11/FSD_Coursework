<div class="auth-wrapper" style="min-height: auto; margin-top: 2rem;">
    <div class="auth-card" style="max-width: 500px;">
        <div class="auth-header" style="margin-bottom: 2rem;">
            <h1 style="font-size: 1.5rem; text-align: left;">Create New Admin</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Regular admins can manage inventory but not users.</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
                <ul style="margin: 0; padding-left: 1.25rem;">
                    <?php foreach ($errors as $err): ?>
                        <li><?= h($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="add_user.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token(); ?>">
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" 
                       value="<?= h($username); ?>" required placeholder="e.g. jdoe_admin">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary" style="flex: 2; height: 48px;">
                    <i class="fa-solid fa-user-check"></i> Create Admin
                </button>
                <a href="users.php" class="btn btn-secondary" style="flex: 1; height: 48px; border: 1px solid var(--border);">Cancel</a>
            </div>
        </form>
    </div>
</div>
