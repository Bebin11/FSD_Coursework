<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_login();

// Get ID
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

// Fetch existing data
$stmt = $pdo->prepare("SELECT * FROM shoes WHERE id = :id");
$stmt->execute(['id' => $id]);
$shoe = $stmt->fetch();

if (!$shoe) {
    set_flash('danger', 'Shoe not found.');
    header("Location: index.php");
    exit;
}

// Fetch dropdown data
try {
    $brands = $pdo->query("SELECT * FROM brands ORDER BY name")->fetchAll();
    $types = $pdo->query("SELECT * FROM shoe_types ORDER BY name")->fetchAll();
    $categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
    $colors = $pdo->query("SELECT * FROM colors ORDER BY name")->fetchAll();
    
    // Fetch existing variants
    $stmtVar = $pdo->prepare("SELECT v.*, c.name as color_name FROM shoe_variants v JOIN colors c ON v.color_id = c.id WHERE v.shoe_id = :id");
    $stmtVar->execute(['id' => $id]);
    $existing_variants = $stmtVar->fetchAll();
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

$errors = [];
$form_data = $shoe; // Initialize with existing data
$form_data['variants'] = $existing_variants;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_data['name'] = trim($_POST['name'] ?? '');
    $form_data['brand_id'] = $_POST['brand_id'] ?? '';
    $form_data['type_id'] = $_POST['type_id'] ?? '';
    $form_data['category_id'] = $_POST['category_id'] ?? '';
    $form_data['price'] = $_POST['price'] ?? '';
    $form_data['description'] = $_POST['description'] ?? '';

    // Variants input
    $sizes = $_POST['sizes'] ?? [];
    $colors_input = $_POST['colors'] ?? [];
    $stocks = $_POST['stocks'] ?? [];

    // Validation
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

            // 1. Update Shoe Master
            $sql = "UPDATE shoes SET 
                    name = :name, 
                    brand_id = :brand_id, 
                    type_id = :type_id, 
                    category_id = :category_id, 
                    price = :price, 
                    description = :description 
                    WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'name' => $form_data['name'],
                'brand_id' => $form_data['brand_id'],
                'type_id' => $form_data['type_id'],
                'category_id' => $form_data['category_id'],
                'price' => $form_data['price'],
                'description' => $form_data['description'],
                'id' => $id
            ]);

            // 2. Sync Variants (Delete and Re-insert is cleanest)
            $pdo->prepare("DELETE FROM shoe_variants WHERE shoe_id = ?")->execute([$id]);
            
            $stmtVarInsert = $pdo->prepare("INSERT INTO shoe_variants (shoe_id, size, color_id, stock_quantity) VALUES (?, ?, ?, ?)");
            for ($i = 0; $i < count($sizes); $i++) {
                $size = $sizes[$i];
                $color_id = $colors_input[$i];
                $stock = (int)($stocks[$i] ?? 0);

                if (!empty($size) && !empty($color_id)) {
                    // Ensure stock is not negative
                    if ($stock < 0) $stock = 0;
                    $stmtVarInsert->execute([$id, $size, $color_id, $stock]);
                }
            }

            $pdo->commit();
            set_flash('success', 'Shoe and variants updated successfully!');
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Database Error: " . $e->getMessage();
        }
    }
}

// Render Form
render_view('inventory/form', [
    'title' => 'Edit Shoe',
    'page' => 'edit',
    'form_data' => $form_data, // Contains either existing DB data or POSTed data on error
    'errors' => $errors,
    'brands' => $brands,
    'types' => $types,
    'categories' => $categories,
    'colors' => $colors,
    'action' => 'edit.php?id=' . $id,
    'submit_text' => 'Update Shoe'
]);
?>
