<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_login();

// Fetch dropdown data
try {
    $brands = $pdo->query("SELECT * FROM brands ORDER BY name")->fetchAll();
    $types = $pdo->query("SELECT * FROM shoe_types ORDER BY name")->fetchAll();
    $categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
    $colors = $pdo->query("SELECT * FROM colors ORDER BY name")->fetchAll();
} catch (PDOException $e) {
    die('<div style="padding: 2rem; text-align: center; font-family: sans-serif;">
        <h1>System Update Required</h1>
        <p>The database schema needs to be updated to support new features.</p>
        <p>Please <a href="migrate_v2.php" style="color: blue; text-decoration: underline;">click here to run the migration script</a>.</p>
        <small style="color: red;">Error: ' . $e->getMessage() . '</small>
    </div>');
}

$errors = [];
$form_data = [
    'name' => '',
    'brand_id' => '',
    'type_id' => '',
    'category_id' => '',
    'price' => '',
    'description' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_data['name'] = trim($_POST['name'] ?? '');
    $form_data['brand_id'] = $_POST['brand_id'] ?? '';
    $form_data['type_id'] = $_POST['type_id'] ?? '';
    $form_data['category_id'] = $_POST['category_id'] ?? '';
    $form_data['price'] = $_POST['price'] ?? '';
    $form_data['description'] = $_POST['description'] ?? '';
    
    // Variants: Expecting array of [size, color_id, stock_quantity]
    // In PHP POST, this usually comes as variants[0][size], variants[0][color]...
    // But we will likely use a dynamic form sending arrays like sizes[], colors[], stocks[]
    $sizes = $_POST['sizes'] ?? [];
    $colors_input = $_POST['colors'] ?? [];
    $stocks = $_POST['stocks'] ?? [];

    // Validation (Server Side)
    if (empty($form_data['name'])) $errors[] = "Shoe Name is required.";
    if (empty($form_data['brand_id'])) $errors[] = "Brand is required.";
    if (empty($form_data['type_id'])) $errors[] = "Type is required.";
    if (empty($form_data['category_id'])) $errors[] = "Category is required.";
    if (empty($form_data['price']) || !is_numeric($form_data['price'])) $errors[] = "Valid Price is required.";
    
    if (empty($sizes)) $errors[] = "At least one product variant is required.";

    if (empty($errors)) {
        verify_csrf_token(); 

        try {
            $pdo->beginTransaction();

            $sql = "INSERT INTO shoes (name, brand_id, type_id, category_id, price, description) 
                    VALUES (:name, :brand_id, :type_id, :category_id, :price, :description)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'name' => $form_data['name'],
                'brand_id' => $form_data['brand_id'],
                'type_id' => $form_data['type_id'],
                'category_id' => $form_data['category_id'],
                'price' => $form_data['price'],
                'description' => $form_data['description']
            ]);
            
            $shoe_id = $pdo->lastInsertId();

            // Insert Variants
            $stmtVar = $pdo->prepare("INSERT INTO shoe_variants (shoe_id, size, color_id, stock_quantity) VALUES (?, ?, ?, ?)");
            
            for ($i = 0; $i < count($sizes); $i++) {
                $size = $sizes[$i];
                $color = $colors_input[$i];
                $stock = (int)($stocks[$i] ?? 0);

                if (!empty($size) && !empty($color)) {
                    // Ensure stock is not negative
                    if ($stock < 0) $stock = 0;
                    $stmtVar->execute([$shoe_id, $size, $color, $stock]);
                }
            }

            $pdo->commit();
            set_flash('success', 'Product added successfully with variants!');
            header("Location: index.php");
            exit;

        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Database Error: " . $e->getMessage();
        }
    } else {
        set_flash('danger', 'Please fix the errors below to continue.');
    }
}

// Render Form
render_view('inventory/form', [
    'title' => 'Add New Shoe',
    'page' => 'add',
    'form_data' => $form_data,
    'errors' => $errors,
    'brands' => $brands,
    'types' => $types,
    'categories' => $categories,
    'colors' => $colors,
    'action' => 'add.php',
    'submit_text' => 'Create Shoe'
]);
?>
