<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$shoe_id = $_GET['id'] ?? null;

if (!$shoe_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing shoe ID']);
    exit;
}

try {
    // 1. Fetch Shoe metadata
    $stmt = $pdo->prepare("
        SELECT s.*, b.name as brand_name, t.name as type_name, c.name as category_name
        FROM shoes s
        LEFT JOIN brands b ON s.brand_id = b.id
        LEFT JOIN shoe_types t ON s.type_id = t.id
        LEFT JOIN categories c ON s.category_id = c.id
        WHERE s.id = ?
    ");
    $stmt->execute([$shoe_id]);
    $shoe = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$shoe) {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
        exit;
    }

    // 2. Fetch variants
    $stmt = $pdo->prepare("
        SELECT v.id, v.size, v.stock_quantity, c.name as color_name, c.hex_code 
        FROM shoe_variants v
        JOIN colors c ON v.color_id = c.id
        WHERE v.shoe_id = ?
        ORDER BY v.size ASC
    ");
    $stmt->execute([$shoe_id]);
    $variants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'shoe' => $shoe,
        'variants' => $variants
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error', 'message' => $e->getMessage()]);
}
?>
