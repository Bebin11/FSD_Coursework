<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_login();

// AJAX Controller for Search/Filter
// V2: Agnostic of single-stock. Aggregates variants.

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

// Search Logic
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
$order_by = "s.created_at DESC"; 
if (!empty($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price_low': $order_by = "s.price ASC"; break;
        case 'price_high': $order_by = "s.price DESC"; break;
        case 'stock_low': $order_by = "total_stock ASC"; break;
        case 'latest': $order_by = "s.created_at DESC"; break;
    }
}

// Group By Product (distinct shoes)
$query .= " GROUP BY s.id, b.name, t.name, c.name ORDER BY $order_by";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $shoes = $stmt->fetchAll();
} catch (PDOException $e) {
    // If table doesn't exist yet (migration pending), handle gracefully or return empty
    $shoes = [];
    error_log("Search Error: " . $e->getMessage());
}

if (count($shoes) > 0) {
    foreach ($shoes as $shoe) {
        $name = h($shoe['name']);
        $brand = h($shoe['brand_name']);
        $cat = h($shoe['category_name']);
        $type = h($shoe['type_name']);
        $price = number_format($shoe['price']);
        
        $sizes = $shoe['sizes'] ? h($shoe['sizes']) : '<span class="text-muted">N/A</span>';
        $sizes = $shoe['sizes'] ? h($shoe['sizes']) : '<span class="text-muted">N/A</span>';
        
        // Render Color Swatches
        $colorHtml = '';
        if (!empty($shoe['color_data'])) {
            $colorsRaw = explode(';;', $shoe['color_data']);
            $colorHtml .= '<div style="display: flex; gap: 4px; flex-wrap: nowrap; overflow-x: auto; padding-bottom: 2px;">';
            foreach ($colorsRaw as $cVal) {
                $parts = explode('|', $cVal);
                $cName = $parts[0] ?? 'Unknown';
                $cHex = $parts[1] ?? '#ccc';
                $colorHtml .= '<div title="'.h($cName).'" style="width: 14px; height: 14px; border-radius: 50%; background-color: '.h($cHex).'; border: 1px solid #ddd; flex-shrink: 0;"></div>';
            }
            $colorHtml .= '</div>';
        } else {
            $colorHtml = '<span class="text-muted">–</span>';
        }
        
        // Stock Logic
        $stockQty = (int)$shoe['total_stock'];
        $stockBadge = ($stockQty < 5) 
            ? '<span class="badge badge-danger">Low: ' . $stockQty . '</span>'
            : '<span class="badge badge-success">' . $stockQty . ' in stock</span>';
        
        // Admin Actions
        $adminActions = '';
        if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'superadmin') {
            $csrf = generate_csrf_token();
            $adminActions = <<<HTML
            <form action="delete.php" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
                <input type="hidden" name="csrf_token" value="$csrf">
                <input type="hidden" name="id" value="{$shoe['id']}">
                <button type="submit" class="btn btn-danger" title="Delete">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>
HTML;
        }

        echo <<<HTML
        <tr>
            <td>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 32px; height: 32px; background: #e0e7ff; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                        <i class="fa-solid fa-shoe-prints"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600;">$name</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">{$brand}</div>
                    </div>
                </div>
            </td>
            <td>{$cat}</td>
            <td><span class="badge badge-primary">{$type}</span></td>
            <td style="white-space: nowrap;">Rs. {$price}</td>
            <td>
                <div style="display: flex; flex-direction: column; gap: 4px;">
                    <span style="font-size: 0.85rem; font-weight: 600;">Multiple Variations</span>
                    {$stockBadge}
                </div>
            </td>
            <td style="text-align: right;">
                <button type="button" class="btn btn-secondary view-variants-btn" 
                        data-id="{$shoe['id']}" 
                        data-name="{$name}" 
                        title="View Details">
                    <i class="fa-solid fa-eye"></i>
                </button>
                <a href="edit.php?id={$shoe['id']}" class="btn btn-secondary" title="Edit">
                    <i class="fa-solid fa-pen"></i>
                </a>
                {$adminActions}
            </td>
        </tr>
HTML;
    }
} else {
    echo <<<HTML
    <tr>
        <td colspan="8" style="text-align: center; padding: 3rem; color: var(--text-muted);">
            <i class="fa-solid fa-box-open" style="font-size: 2rem; margin-bottom: 1rem;"></i><br>
            No inventory found.
        </td>
    </tr>
HTML;
}
?>
