<!-- Stats Widgets -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Inventory</div>
        <div class="stat-value">
            <i class="fa-solid fa-boxes-stacked" style="color: var(--primary); font-size: 0.7em;"></i>
            <?= number_format($stats['total']); ?>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Low Stock Alerts</div>
        <div class="stat-value" style="color: <?= $stats['low_stock'] > 0 ? 'var(--danger-text)' : 'inherit'; ?>">
            <i class="fa-solid fa-triangle-exclamation" style="font-size: 0.7em;"></i>
            <?= number_format($stats['low_stock']); ?>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Asset Value</div>
        <div class="stat-value">
            <span style="font-size: 0.6em; color: var(--text-muted);">Rs.</span>
            <?= number_format($stats['value']); ?>
        </div>
    </div>
</div>

<!-- Filters & Actions -->
<div class="filter-bar">
    <div style="flex: 2; min-width: 250px;">
        <input type="text" id="q" name="q" placeholder="Search by shoe name or brand..." 
               value="<?= h($filters['q'] ?? ''); ?>" style="width: 100%;">
    </div>
    
    <div class="filter-group">
        <select id="filter_brand" name="brand_id">
            <option value="">All Brands</option>
            <?php foreach ($brands as $brand): ?>
                <option value="<?= $brand['id']; ?>" <?= ($filters['brand_id'] ?? '') == $brand['id'] ? 'selected' : ''; ?>>
                    <?= h($brand['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="filter-group">
        <select id="filter_cat" name="category_id">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id']; ?>" <?= ($filters['category_id'] ?? '') == $cat['id'] ? 'selected' : ''; ?>>
                    <?= h($cat['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="filter-group">
        <select id="filter_type" name="type_id">
            <option value="">All Types</option>
            <?php foreach ($types as $type): ?>
                <option value="<?= $type['id']; ?>" <?= ($filters['type_id'] ?? '') == $type['id'] ? 'selected' : ''; ?>>
                    <?= h($type['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="filter-group">
        <select id="filter_sort" name="sort">
            <option value="latest" <?= ($filters['sort'] ?? '') == 'latest' ? 'selected' : ''; ?>>Latest First</option>
            <option value="price_low" <?= ($filters['sort'] ?? '') == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
            <option value="price_high" <?= ($filters['sort'] ?? '') == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
            <option value="stock_low" <?= ($filters['sort'] ?? '') == 'stock_low' ? 'selected' : ''; ?>>Low Stock First</option>
        </select>
    </div>
</div>

<!-- Data Table -->
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Type</th>
                <th>Price</th>
                <th>Inventory Summary</th>
                <th style="text-align: right;">Actions</th>
            </tr>
        </thead>
        <tbody id="inventory-body">
            <?php if (count($shoes) > 0): ?>
                <?php foreach ($shoes as $shoe): ?>
                    <?php
                        $stockQty = (int)($shoe['total_stock'] ?? 0);
                        $variantCount = 0;
                        if (!empty($shoe['color_data'])) {
                             $variantCount = count(explode(';;', $shoe['color_data']));
                        }
                    ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 32px; height: 32px; background: #e0e7ff; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                                    <i class="fa-solid fa-shoe-prints"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 600;"><?= h($shoe['name']); ?></div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?= h($shoe['brand_name']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td><?= h($shoe['category_name']); ?></td>
                        <td><span class="badge badge-primary"><?= h($shoe['type_name']); ?></span></td>
                        <td>Rs. <?= number_format($shoe['price']); ?></td>
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <span style="font-size: 0.85rem; font-weight: 600;"><?= $variantCount; ?> Variations</span>
                                <?php if ($stockQty < 5): ?>
                                    <span class="badge badge-danger">Low: <?= $stockQty; ?> Total</span>
                                <?php else: ?>
                                    <span class="badge badge-success"><?= $stockQty; ?> Total Stock</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td style="text-align: right;">
                            <button type="button" class="btn btn-secondary view-variants-btn" 
                                    data-id="<?= $shoe['id']; ?>" 
                                    data-name="<?= h($shoe['name']); ?>" 
                                    title="View Details">
                                <i class="fa-solid fa-eye"></i>
                            </button>

                            <a href="edit.php?id=<?= $shoe['id']; ?>" class="btn btn-secondary" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            
                            <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'superadmin'): ?>
                                <form action="delete.php" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token(); ?>">
                                    <input type="hidden" name="id" value="<?= $shoe['id']; ?>">
                                    <button type="submit" class="btn btn-danger" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                        <i class="fa-solid fa-box-open" style="font-size: 2rem; margin-bottom: 1rem;"></i><br>
                        No inventory found matching your criteria.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Variants Modal -->
<div id="variantsModal" class="modal-backdrop" onclick="if(event.target === this) closeVariants()">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Product Details</h3>
            <button class="modal-close" onclick="closeVariants()">&times;</button>
        </div>
        <div class="modal-body">
            <div id="modalLoading" style="text-align: center; padding: 3rem;">
                <i class="fa-solid fa-spinner fa-spin" style="font-size: 2rem; color: #6366f1;"></i>
            </div>
            <div id="modalContent" style="display: none;">
                <!-- Product Metadata -->
                <div class="modal-product-info">
                    <h2 id="modalShoeName" style="font-size: 1.75rem; font-weight: 800; margin-bottom: 0.75rem; color: #0f172a;"></h2>
                    <p id="modalDescription" style="margin-bottom: 2rem; color: #64748b; line-height: 1.6;"></p>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Brand:</span>
                            <span class="info-value" id="modalBrand">-</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Category:</span>
                            <span class="info-value" id="modalCategory">-</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Type:</span>
                            <span class="info-value" id="modalType">-</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Base Price:</span>
                            <span class="info-value" id="modalPrice">-</span>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 3rem;">
                    <h4 style="font-size: 0.9rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                        <i class="fa-solid fa-layer-group" style="font-size: 0.8em; opacity: 0.5;"></i>
                        Stock Breakdown
                    </h4>
                    <table class="variant-detail-table">
                    <thead>
                        <tr>
                            <th>Size</th>
                            <th>Color</th>
                            <th>Stock Status</th>
                        </tr>
                    </thead>
                    <tbody id="variantsTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
