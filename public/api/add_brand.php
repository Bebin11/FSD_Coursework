<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data['name'] ?? '');

    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Brand name is required']);
        exit;
    }

    try {
        // Check if exists
        $stmt = $pdo->prepare("SELECT id FROM brands WHERE name = ?");
        $stmt->execute([$name]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Brand already exists']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO brands (name) VALUES (?)");
        $stmt->execute([$name]);
        
        echo json_encode([
            'success' => true, 
            'id' => $pdo->lastInsertId(),
            'name' => $name
        ]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}
?>
