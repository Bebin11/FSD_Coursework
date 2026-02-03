<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Strict RBAC: Only Admin can delete
require_admin(); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Check
    verify_csrf_token();

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM shoes WHERE id = :id");
        if ($stmt->execute(['id' => $id])) {
            set_flash('success', 'Shoe deleted successfully.');
        } else {
            set_flash('danger', 'Failed to delete shoe.');
        }
    } else {
        set_flash('danger', 'Invalid ID.');
    }
}

header("Location: index.php");
exit;
?>
