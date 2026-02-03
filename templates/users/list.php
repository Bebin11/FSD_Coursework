<div class="table-container">
    <div style="padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border);">
        <h3 style="font-weight: 700;">Manage Administrative Accounts</h3>
        <a href="add_user.php" class="btn btn-primary">
            <i class="fa-solid fa-user-plus"></i> Add New Admin
        </a>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Role</th>
                <th>Joined</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 32px; height: 32px; background: #e0e7ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--primary);">
                                <?= strtoupper(substr($u['username'], 0, 1)); ?>
                            </div>
                            <span style="font-weight: 600;"><?= h($u['username']); ?></span>
                        </div>
                    </td>
                    <td>
                        <span class="badge <?= $u['role'] === 'superadmin' ? 'badge-primary' : 'badge-warning'; ?>">
                            <?= h(ucfirst($u['role'])); ?>
                        </span>
                    </td>
                    <td><?= date('M j, Y', strtotime($u['created_at'])); ?></td>
                    <td style="text-align: right;">
                        <?php if ($u['id'] !== $_SESSION['user_id'] && $u['role'] !== 'superadmin'): ?>
                            <form action="users.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this admin?');" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token(); ?>">
                                <input type="hidden" name="delete_id" value="<?= $u['id']; ?>">
                                <button type="submit" class="btn btn-danger" style="padding: 0.5rem 0.75rem;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        <?php else: ?>
                            <span class="text-muted" style="font-size: 0.8rem;">System Account</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
