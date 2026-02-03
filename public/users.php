<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_superadmin();

// Delete User Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    verify_csrf_token();
    $deleteId = (int)$_POST['delete_id'];
    
    // Cannot delete yourself
    if ($deleteId === $_SESSION['user_id']) {
        set_flash('danger', 'You cannot delete your own account.');
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'superadmin'");
        $stmt->execute([$deleteId]);
        set_flash('success', 'User deleted successfully.');
    }
    header("Location: users.php");
    exit;
}

// Fetch all admins
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();

render_view('users/list', [
    'title' => 'User Management',
    'page' => 'users',
    'users' => $users
]);
