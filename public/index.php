<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_login();

// 1. Fetch Stats for Dashboard Widgets (V2)
$total_shoes = $pdo->query("SELECT COUNT(*) FROM shoes")->fetchColumn();

// Low Stock: Count variants with stock < 5
$low_stock = $pdo->query("SELECT COUNT(*) FROM shoe_variants WHERE stock_quantity < 5")->fetchColumn();

// Total Value: Sum of (Price * Stock) across all variants
$total_value = $pdo->query("
    SELECT SUM(s.price * v.stock_quantity) 
    FROM shoes s 
    JOIN shoe_variants v ON s.id = v.shoe_id
")->fetchColumn();

// 2. Fetch Dropdowns
$brands = $pdo->query("SELECT * FROM brands ORDER BY name")->fetchAll();
$types = $pdo->query("SELECT * FROM shoe_types ORDER BY name")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// 3. Build Inventory Query (V2 - Variants Aggregation)
$query = "SELECT s.*, 
                 b.name AS brand_name, 
                 t.name AS type_name, 
                 c.name AS category_name,
                 SUM(v.stock_quantity) as total_stock,
                 GROUP_CONCAT(DISTINCT v.size ORDER BY v.size ASC SEPARATOR ', ') as sizes,
                 GROUP_CONCAT(DISTINCT CONCAT(col.name, '|', col.hex_code) SEPARATOR ';;') as color_data
          FROM shoes s
          LEFT JOIN brands b ON s.brand_id = b.id
          LEFT JOIN shoe_types t ON s.type_id = t.id
          LEFT JOIN categories c ON s.category_id = c.id
          LEFT JOIN shoe_variants v ON s.id = v.shoe_id
          LEFT JOIN colors col ON v.color_id = col.id
          WHERE 1=1";

$params = [];

if (!empty($_GET['q'])) {
    $q = trim($_GET['q']);
    $query .= " AND (s.name LIKE :q1 
                 OR b.name LIKE :q2 
                 OR c.name LIKE :q3 
                 OR t.name LIKE :q4 
                 OR s.description LIKE :q5 
                 OR v.size LIKE :q6 
                 OR col.name LIKE :q7)";
    $params['q1'] = '%' . $q . '%';
    $params['q2'] = '%' . $q . '%';
    $params['q3'] = '%' . $q . '%';
    $params['q4'] = '%' . $q . '%';
    $params['q5'] = '%' . $q . '%';
    $params['q6'] = '%' . $q . '%';
    $params['q7'] = '%' . $q . '%';
}
if (!empty($_GET['brand_id'])) {
    $query .= " AND s.brand_id = :brand_id";
    $params['brand_id'] = $_GET['brand_id'];
}
if (!empty($_GET['category_id'])) {
    $query .= " AND s.category_id = :category_id";
    $params['category_id'] = $_GET['category_id'];
}
if (!empty($_GET['type_id'])) {
    $query .= " AND s.type_id = :type_id";
    $params['type_id'] = $_GET['type_id'];
}

// Sorting Logic
$order_by = "s.created_at DESC"; // Default
if (!empty($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price_low': $order_by = "s.price ASC"; break;
        case 'price_high': $order_by = "s.price DESC"; break;
        case 'stock_low': $order_by = "total_stock ASC"; break;
        case 'latest': $order_by = "s.created_at DESC"; break;
    }
}

$query .= " GROUP BY s.id, b.name, t.name, c.name ORDER BY $order_by";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $shoes = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Inventory Query Failed: " . $e->getMessage());
}

// Render View
render_view('inventory/list', [
    'title' => 'Dashboard Overview',
    'page' => 'dashboard',
    'stats' => [
        'total' => $total_shoes,
        'low_stock' => $low_stock,
        'value' => $total_value
    ],
    'shoes' => $shoes,
    'brands' => $brands,
    'types' => $types,
    'categories' => $categories,
    'filters' => $_GET
]);
?>
