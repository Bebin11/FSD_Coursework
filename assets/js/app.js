/**
 * Khutta Ma Jutta - Frontend Logic
 */

// --- 1. Variant Repeater Logic ---
function addVariantRow() {
    const list = document.getElementById('variant-list');
    const template = document.getElementById('variant-template');

    // Safety check
    if (!list || !template) {
        console.warn('Variant List or Template not found');
        return;
    }

    try {
        const clone = template.content.cloneNode(true);
        list.appendChild(clone);
    } catch (e) {
        console.error('Error adding variant row:', e);
    }
}

function removeVariantRow(btn) {
    const block = btn.closest('.variant-block');
    if (block) block.remove();
}

// --- 2. Brand Modal Logic ---
function openBrandModal(e) {
    if (e) e.preventDefault();
    const modal = document.getElementById('brandModal');
    if (modal) modal.style.display = 'flex';
}

function closeBrandModal() {
    const modal = document.getElementById('brandModal');
    if (modal) modal.style.display = 'none';

    const input = document.getElementById('new_brand_name');
    if (input) input.value = '';
}

function submitNewBrand() {
    const nameInput = document.getElementById('new_brand_name');
    if (!nameInput) return;

    const name = nameInput.value.trim();
    if (!name) return;

    fetch('api/add_brand.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name: name })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Add to dropdown
                const select = document.getElementById('brand');
                if (select) {
                    const option = document.createElement('option');
                    option.value = data.id;
                    option.textContent = data.name;
                    option.selected = true;
                    select.appendChild(option);
                }
                closeBrandModal();
            } else {
                alert(data.message || 'Error adding brand');
            }
        })
        .catch(err => alert('Error connecting to server'));
}

// --- 3. Search Logic ---
function runSearchLogic() {
    const qInput = document.getElementById('q');
    // If we are not on the dashboard/search page, exit gracefully
    if (!qInput) return;

    const q = qInput.value;
    const brand = document.getElementById('filter_brand')?.value || '';
    const cat = document.getElementById('filter_cat')?.value || '';
    const type = document.getElementById('filter_type')?.value || '';
    const sort = document.getElementById('filter_sort')?.value || '';

    const params = new URLSearchParams();
    if (q) params.set('q', q);
    if (brand) params.set('brand_id', brand);
    if (cat) params.set('category_id', cat);
    if (type) params.set('type_id', type);
    if (sort) params.set('sort', sort);

    // Update URL
    const newUrl = `${window.location.pathname}?${params.toString()}`;
    window.history.replaceState(null, '', newUrl);

    // Fetch Results
    fetch(`search.php?${params.toString()}`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.text();
        })
        .then(html => {
            const tbody = document.getElementById('inventory-body');
            if (tbody) {
                tbody.innerHTML = html;
            }
        })
        .catch(err => {
            console.error('Search Fetch Error:', err);
            // Fallback for user
            alert('Search failed. Check console for details.');
        });
}

// Debounce Utility
function debounce(func, wait) {
    let timeout;
    return function (...args) {
        const context = this;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}

// --- 4. Initialization ---
document.addEventListener('DOMContentLoaded', () => {
    // A. Bind Search Inputs
    const searchInput = document.getElementById('q');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(runSearchLogic, 300));
    }

    // B. Bind Filters
    const filterIds = ['filter_brand', 'filter_cat', 'filter_type', 'filter_sort'];
    filterIds.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', runSearchLogic);
    });

    // C. Bind Variant Repeater (Add Product Page)
    const list = document.getElementById('variant-list');
    if (list && list.children.length === 0) {
        // Automatically add one empty row if list is empty
        addVariantRow();
    }
    // D. Delegation for Variants Modal (Dashboard & Search)
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.view-variants-btn');
        if (btn) {
            const shoeId = btn.getAttribute('data-id');
            const shoeName = btn.getAttribute('data-name');
            if (shoeId && shoeName) {
                viewVariants(shoeId, shoeName);
            }
        }
    });
});

// --- 5. Global Exports (for onclick=...) ---
window.addVariantRow = addVariantRow;
window.removeVariantRow = removeVariantRow;
window.openBrandModal = openBrandModal;
window.closeBrandModal = closeBrandModal;
window.submitNewBrand = submitNewBrand;
window.runSearch = runSearchLogic;
window.debounceSearch = debounce(runSearchLogic, 300);

// --- 6. Variants Modal Logic ---
function viewVariants(shoeId, shoeName) {
    const modal = document.getElementById('variantsModal');
    const nameEl = document.getElementById('modalShoeName');
    const loadingEl = document.getElementById('modalLoading');
    const contentEl = document.getElementById('modalContent');
    const tableBody = document.getElementById('variantsTableBody');

    if (!modal) return;

    // Show modal and loading
    nameEl.textContent = shoeName;
    loadingEl.style.display = 'block';
    contentEl.style.display = 'none';
    modal.style.display = 'flex';
    tableBody.innerHTML = '';

    fetch(`api/get_variants.php?id=${shoeId}`)
        .then(res => res.json())
        .then(data => {
            const shoe = data.shoe;
            const variants = data.variants;

            loadingEl.style.display = 'none';
            contentEl.style.display = 'block';

            // Populate Metadata
            document.getElementById('modalDescription').textContent = shoe.description || 'No description available.';
            document.getElementById('modalBrand').textContent = shoe.brand_name;
            document.getElementById('modalCategory').textContent = shoe.category_name;
            document.getElementById('modalType').textContent = shoe.type_name;
            document.getElementById('modalPrice').textContent = 'Rs. ' + new Intl.NumberFormat().format(shoe.price);

            if (variants.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="3" style="text-align:center;">No variants found.</td></tr>';
                return;
            }

            variants.forEach(v => {
                const stockBadge = v.stock_quantity < 5
                    ? `<span class="badge badge-danger">Low: ${v.stock_quantity}</span>`
                    : `<span class="badge badge-success">${v.stock_quantity} in stock</span>`;

                tableBody.innerHTML += `
                    <tr>
                        <td><strong>${v.size}</strong></td>
                        <td>
                            <span class="color-dot" style="background-color: ${v.hex_code}"></span>
                            ${v.color_name}
                        </td>
                        <td>${stockBadge}</td>
                    </tr>
                `;
            });
        })
        .catch(err => {
            console.error('Fetch error:', err);
            loadingEl.innerHTML = '<p style="color:var(--danger-text)">Failed to load data.</p>';
        });
}

function closeVariants() {
    const modal = document.getElementById('variantsModal');
    if (modal) modal.style.display = 'none';
}

// Close on Escape
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeVariants();
});

// Re-expose to window
window.viewVariants = viewVariants;
window.closeVariants = closeVariants;
