<div class="table-container" style="padding: 2.5rem; background: var(--bg-surface);">
    <div style="max-width: 1000px; margin: 0 auto;">
        <div style="margin-bottom: 2.5rem; border-bottom: 1px solid var(--border); padding-bottom: 1.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-main); margin: 0;"><?= h($title); ?></h2>
        </div>

        <?php if (!empty($errors)): ?>
             <div class="alert alert-danger" style="margin-bottom: 2rem; border-radius: 8px;">
                 <ul style="margin: 0; padding-left: 1.5rem;">
                     <?php foreach ($errors as $err): ?>
                         <li><?= h($err); ?></li>
                     <?php endforeach; ?>
                 </ul>
             </div>
        <?php endif; ?>

        <form action="<?= $action; ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token(); ?>">

            <!-- Core Info -->
            <div style="margin-bottom: 2.5rem;">
                <h4 style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1.25rem;">
                    Basic Information
                </h4>
                
                <div class="form-group">
                    <label for="name">Shoe Name</label>
                    <input type="text" id="name" name="name" class="form-control" 
                           value="<?= h($form_data['name']); ?>" required placeholder="e.g. Nike Air Max">
                </div>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                    <div class="form-group">
                        <label for="brand">Brand <a href="#" onclick="openBrandModal(event)" style="font-size: 0.75rem; color: var(--primary); text-decoration: none;">+ Add Brand</a></label>
                        <select id="brand" name="brand_id" required>
                            <option value="">Select Brand</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?= $brand['id']; ?>" <?= $form_data['brand_id'] == $brand['id'] ? 'selected' : ''; ?>>
                                    <?= h($brand['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id']; ?>" <?= $form_data['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                    <?= h($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="type">Type</label>
                        <select id="type" name="type_id" required>
                            <option value="">Select Type</option>
                            <?php foreach ($types as $type): ?>
                                <option value="<?= $type['id']; ?>" <?= $form_data['type_id'] == $type['id'] ? 'selected' : ''; ?>>
                                    <?= h($type['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div style="margin-bottom: 2.5rem;">
                <h4 style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1.25rem;">
                    Pricing & Description
                </h4>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3" 
                              style="min-height: 80px;"
                              placeholder="Product details..."><?= h($form_data['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group" style="max-width: 250px;">
                    <label for="price">Unit Price (Rs.)</label>
                    <input type="number" step="0.01" id="price" name="price" class="form-control"
                           value="<?= h($form_data['price']); ?>" required placeholder="0.00">
                </div>
            </div>

            <!-- Variants -->
            <div style="margin-bottom: 3rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <h4 style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">
                        Inventory Variants
                    </h4>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="addVariantRow()" style="padding: 0.5rem 1rem;">
                        <i class="fa-solid fa-plus"></i> Add Variant
                    </button>
                </div>
                
                <div id="variant-list">
                    <?php 
                    $vars = $form_data['variants'] ?? [];
                    if (empty($vars)) $vars = [['size' => '', 'color_id' => '', 'stock_quantity' => '']];
                    foreach ($vars as $v): 
                    ?>
                        <div class="variant-block">
                            <div class="variant-field">
                                <label>Size</label>
                                <select name="sizes[]" required>
                                    <option value="">Size</option>
                                    <?php for($s=35; $s<=46; $s++): ?>
                                        <option value="<?= $s; ?>" <?= ($v['size'] ?? '') == $s ? 'selected' : ''; ?>><?= $s; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="variant-field">
                                <label>Color</label>
                                <select name="colors[]" required>
                                    <option value="">Color</option>
                                    <?php foreach ($colors as $color): ?>
                                        <option value="<?= $color['id']; ?>" <?= ($v['color_id'] ?? '') == $color['id'] ? 'selected' : ''; ?>>
                                            <?= h($color['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="variant-field">
                                <label>Stock</label>
                                <input type="number" name="stocks[]" required min="0" value="<?= (int)($v['stock_quantity'] ?? 0); ?>" placeholder="0">
                            </div>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeVariantRow(this)">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <template id="variant-template">
                    <div class="variant-block">
                        <div class="variant-field">
                            <label>Size</label>
                            <select name="sizes[]" required>
                                <option value="">Size</option>
                                <?php for($s=35; $s<=46; $s++): ?>
                                    <option value="<?= $s; ?>"><?= $s; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="variant-field">
                            <label>Color</label>
                            <select name="colors[]" required>
                                <option value="">Color</option>
                                <?php foreach ($colors as $color): ?>
                                    <option value="<?= $color['id']; ?>"><?= h($color['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="variant-field">
                            <label>Stock</label>
                            <input type="number" name="stocks[]" required min="0" placeholder="0">
                        </div>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeVariantRow(this)">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </template>
            </div>

            <!-- Actions -->
            <div style="display: flex; gap: 1rem; margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--border);">
                <button type="submit" class="btn btn-primary" style="flex: 2; height: 48px; border-radius: 8px;">
                    <i class="fa-solid fa-save"></i> <?= h($submit_text); ?>
                </button>
                <a href="index.php" class="btn btn-secondary" style="flex: 1; height: 48px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<!-- Modal Background for New Brand -->
<div id="brandModal" class="modal-backdrop" onclick="if(event.target === this) closeBrandModal()">
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header">
            <h3>Register New Brand</h3>
            <button class="modal-close" onclick="closeBrandModal()">&times;</button>
        </div>
        <div class="modal-body" style="padding: 2.5rem;">
            <div class="form-group">
                <label for="new_brand_name">Brand Name</label>
                <input type="text" id="new_brand_name" class="form-control" placeholder="e.g. Balenciaga">
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="button" class="btn btn-primary" onclick="submitNewBrand()" style="flex: 2;">Confirm</button>
                <button type="button" class="btn btn-secondary" onclick="closeBrandModal()" style="flex: 1;">Cancel</button>
            </div>
        </div>
    </div>
</div>
