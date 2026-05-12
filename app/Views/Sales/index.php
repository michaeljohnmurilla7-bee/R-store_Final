<?= $this->extend('theme/template') ?>
<meta name="csrf-token" content="<?= csrf_hash() ?>">

<?= $this->section('content') ?>

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Sales & Inventory Point of Sale</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
            <li class="breadcrumb-item active">Sales</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <!-- LEFT COLUMN: Sales Panel -->
        <div class="col-md-8">
          <div class="card card-primary card-outline">
            <div class="card-header">
              <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                  <h3 class="card-title">
                    <i class="fas fa-shopping-cart"></i> Current Sale
                  </h3>
                  <span class="badge badge-success ml-2" id="orderStatusBadge">Completed</span>
                </div>
                <div>
                  <span class="text-muted mr-3">
                    <i class="far fa-calendar-alt"></i> 
                    <span id="saleDateTimeDisplay"></span>
                  </span>
                  <button type="button" class="btn btn-sm btn-primary" id="addProductToSaleBtn">
                    <i class="fas fa-plus-circle"></i> Add Product
                  </button>
                </div>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-bordered table-striped table-sm mb-0" id="saleItemsTable">
                  <thead class="thead-light">
                    <tr>
                      <th>Product</th>
                      <th>Price (₱)</th>
                      <th width="100">Quantity</th>
                      <th>Subtotal (₱)</th>
                      <th width="50">Action</th>
                    </tr>
                  </thead>
                  <tbody id="saleItemsBody">
                    <tr id="emptyCartRow">
                      <td colspan="5" class="text-center text-muted">No products added — click "Add Product"</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer">
              <div class="row">
                <div class="col-md-5">
                  <div class="form-group mb-0">
                    <label><i class="fas fa-sticky-note"></i> Notes</label>
                    <input type="text" id="saleNotes" class="form-control form-control-sm" placeholder="Order notes...">
                  </div>
                </div>
                <div class="col-md-7">
                  <div class="row">
                    <div class="col-4">
                      <div class="form-group mb-0">
                        <label>Discount (₱)</label>
                        <input type="number" id="discountAmount" class="form-control form-control-sm" value="0" step="1" min="0">
                      </div>
                    </div>
                    <div class="col-4">
                      <div class="form-group mb-0">
                        <label>Amount Paid (₱)</label>
                        <input type="number" id="amountPaid" class="form-control form-control-sm" value="0" step="10" min="0">
                      </div>
                    </div>
                    <div class="col-4">
                      <div class="form-group mb-0">
                        <label>Change (₱)</label>
                        <input type="text" id="changeDue" class="form-control form-control-sm" readonly value="0.00">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row mt-3">
                <div class="col-6">
                  <button type="button" class="btn btn-danger" id="cancelSaleBtn">
                    <i class="fas fa-trash-alt"></i> Cancel Sale
                  </button>
                </div>
                <div class="col-6 text-right">
                  <button type="button" class="btn btn-success" id="saveSaleBtn">
                    <i class="fas fa-check-circle"></i> Complete Sale
                  </button>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Recent Sales History -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-history"></i> Recent Sales</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                  <thead>
                    <tr>
                      <th>Order ID</th>
                      <th>Date</th>
                      <th>Items</th>
                      <th>Total</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody id="salesHistoryBody">
                    <tr><td colspan="5" class="text-center text-muted">No recent sales</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- RIGHT COLUMN: Inventory Panel -->
        <div class="col-md-4">
          <div class="card card-info">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-boxes"></i> Inventory Summary</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="small-box bg-light p-3 mb-0 border-bottom">
                <div class="row">
                  <div class="col-6"><strong>Categories:</strong></div>
                  <div class="col-6" id="categoryList">-</div>
                </div>
                <div class="row mt-1">
                  <div class="col-6"><strong>Suppliers:</strong></div>
                  <div class="col-6" id="supplierList">-</div>
                </div>
              </div>
              <div class="p-2" style="max-height: 320px; overflow-y: auto;" id="inventoryProductList">
                <div class="text-center text-muted p-3">Loading products...</div>
              </div>
            </div>
          </div>

          <div class="card card-warning">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-chart-line"></i> Reports</h3>
            </div>
            <div class="card-body">
              <button type="button" class="btn btn-outline-warning btn-block mb-3" id="generateReportBtn">
                <i class="fas fa-file-alt"></i> Generate Sales Report
              </button>
              <button type="button" class="btn btn-outline-success btn-block" id="exportSalesBtn">
                <i class="fas fa-download"></i> Export Sales (CSV)
              </button>
              <div id="reportPreview" class="mt-3 p-2 bg-light rounded small" style="min-height: 100px;">
                <span class="text-muted">Click generate to view report summary</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Select Product Modal -->
<div class="modal fade" id="selectProductModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fas fa-search"></i> Select Product to Add</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="text" id="productSearchInput" class="form-control mb-3" placeholder="Search by name, SKU...">
        <div style="max-height: 400px; overflow-y: auto;">
          <table class="table table-sm table-bordered">
            <thead class="thead-light">
              <tr>
                <th>Name</th>
                <th>SKU</th>
                <th>Price (₱)</th>
                <th>Stock</th>
                <th width="50"></th>
              </tr>
            </thead>
            <tbody id="productSelectorBody">
              <tr><td colspan="5" class="text-center">Loading products...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Toast Container -->
<div class="toasts-top-right fixed" style="position: fixed; top: 1rem; right: 1rem; z-index: 9999;"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const baseUrl = "<?= base_url() ?>";

// Global variables
let masterProducts = [];
let currentCart = [];

// Show notification
function showNotification(msg, type = 'info') {
    const toastContainer = document.querySelector('.toasts-top-right');
    if (!toastContainer) return;
    
    const toast = document.createElement('div');
    toast.className = `toast bg-${type === 'error' ? 'danger' : (type === 'success' ? 'success' : 'info')} fade show`;
    toast.setAttribute('role', 'alert');
    toast.style.marginBottom = '10px';
    toast.innerHTML = `
        <div class="toast-header">
            <strong class="mr-auto">RStore</strong>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">&times;</button>
        </div>
        <div class="toast-body">${msg}</div>
    `;
    toastContainer.prepend(toast);
    $(toast).toast({ delay: 3000 }).toast('show');
    $(toast).on('hidden.bs.toast', function() { $(this).remove(); });
}

// Load products from server
async function loadProducts() {
    try {
        showNotification('Loading products...', 'info');
        const response = await fetch(baseUrl + 'sales/getProductsJson', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const result = await response.json();
        
        if (result.status === 'success' && result.data) {
            masterProducts = result.data;
            renderInventoryPanel();
            loadProductSelector();
            showNotification(`Loaded ${masterProducts.length} products`, 'success');
        } else {
            // Fallback demo data
            masterProducts = [
                { id: 1, name: "Coca-Cola", sku: "COKE001", price: 55, stock: 45, category: "Drinks", supplier: "Coca-Cola" },
                { id: 2, name: "Laptop Pro", sku: "LAP001", price: 45000, stock: 12, category: "Electronics", supplier: "TechDistro" },
                { id: 3, name: "Wireless Mouse", sku: "MOU123", price: 650, stock: 45, category: "Electronics", supplier: "TechDistro" },
                { id: 4, name: "Organic Coffee", sku: "COF456", price: 320, stock: 28, category: "Grocery", supplier: "FreshMart" }
            ];
            renderInventoryPanel();
            loadProductSelector();
            showNotification('Using demo products (database connection issue)', 'warning');
        }
    } catch (error) {
        console.error('Error loading products:', error);
        // Fallback demo data
        masterProducts = [
            { id: 1, name: "Coca-Cola", sku: "COKE001", price: 55, stock: 45, category: "Drinks", supplier: "Coca-Cola" },
            { id: 2, name: "Laptop Pro", sku: "LAP001", price: 45000, stock: 12, category: "Electronics", supplier: "TechDistro" },
            { id: 3, name: "Wireless Mouse", sku: "MOU123", price: 650, stock: 45, category: "Electronics", supplier: "TechDistro" },
            { id: 4, name: "Organic Coffee", sku: "COF456", price: 320, stock: 28, category: "Grocery", supplier: "FreshMart" }
        ];
        renderInventoryPanel();
        loadProductSelector();
        showNotification('Using demo products', 'warning');
    }
}

// Render inventory panel
function renderInventoryPanel() {
    const container = document.getElementById("inventoryProductList");
    if (!container) return;
    
    if (!masterProducts.length) {
        container.innerHTML = '<div class="text-center text-muted p-3">No products found</div>';
        return;
    }
    
    // Update categories and suppliers
    const categories = [...new Set(masterProducts.map(p => p.category).filter(c => c))];
    const suppliers = [...new Set(masterProducts.map(p => p.supplier).filter(s => s))];
    document.getElementById("categoryList").innerHTML = categories.length ? categories.join(', ') : '-';
    document.getElementById("supplierList").innerHTML = suppliers.length ? suppliers.join(', ') : '-';
    
    let html = '<div class="list-group list-group-flush">';
    masterProducts.forEach(prod => {
        const stockBadge = prod.stock <= 5 ? 'badge-danger' : (prod.stock <= 10 ? 'badge-warning' : 'badge-success');
        html += `<div class="list-group-item p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${escapeHtml(prod.name)}</strong><br>
                            <small class="text-muted">₱${parseFloat(prod.price).toFixed(2)} | Stock: <span class="badge ${stockBadge}">${prod.stock}</span></small>
                        </div>
                        <button class="btn btn-xs btn-primary add-from-inventory" data-id="${prod.id}" ${prod.stock <= 0 ? 'disabled' : ''}>
                            <i class="fas fa-plus"></i> Add
                        </button>
                    </div>
                 </div>`;
    });
    html += '</div>';
    container.innerHTML = html;
    
    // Attach add buttons
    document.querySelectorAll(".add-from-inventory").forEach(btn => {
        btn.addEventListener("click", (e) => {
            const prodId = parseInt(btn.getAttribute("data-id"));
            const product = masterProducts.find(p => p.id == prodId);
            if (product) addProductToCart(product);
        });
    });
}

// Load product selector modal
function loadProductSelector() {
    const tbody = document.getElementById("productSelectorBody");
    if (!tbody) return;
    
    if (!masterProducts.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No products available</td></tr>';
        return;
    }
    
    let html = '';
    masterProducts.forEach(prod => {
        html += `<tr>
                    <td>${escapeHtml(prod.name)}</td>
                    <td><small>${escapeHtml(prod.sku)}</small></td>
                    <td>₱${parseFloat(prod.price).toFixed(2)}</td>
                    <td><span class="badge ${prod.stock <= 0 ? 'badge-danger' : 'badge-secondary'}">${prod.stock}</span></td>
                    <td>
                        <button class="btn btn-sm btn-success select-product-btn" data-id="${prod.id}" ${prod.stock <= 0 ? 'disabled' : ''}>
                            <i class="fas fa-cart-plus"></i>
                        </button>
                    </td>
                 </tr>`;
    });
    tbody.innerHTML = html;
    
    // Attach select buttons
    document.querySelectorAll(".select-product-btn").forEach(btn => {
        btn.addEventListener("click", (e) => {
            const prodId = parseInt(btn.getAttribute("data-id"));
            const product = masterProducts.find(p => p.id == prodId);
            if (product && product.stock > 0) {
                addProductToCart(product);
                $('#selectProductModal').modal('hide');
                showNotification(`Added ${product.name} to cart`, 'success');
            }
        });
    });
}

// Add product to cart
function addProductToCart(product) {
    if (product.stock <= 0) {
        showNotification(`❌ ${product.name} is out of stock!`, 'error');
        return false;
    }
    
    const existingItem = currentCart.find(item => item.productId == product.id);
    if (existingItem) {
        if (existingItem.quantity + 1 > product.stock) {
            showNotification(`⚠️ Cannot add more than ${product.stock} units`, 'error');
            return false;
        }
        existingItem.quantity += 1;
    } else {
        currentCart.push({
            productId: product.id,
            name: product.name,
            sku: product.sku,
            price: parseFloat(product.price),
            quantity: 1
        });
    }
    renderCartTable();
    return true;
}

// Render cart table
function renderCartTable() {
    const tbody = document.getElementById("saleItemsBody");
    if (!tbody) return;
    
    if (currentCart.length === 0) {
        tbody.innerHTML = '<tr id="emptyCartRow"><td colspan="5" class="text-center text-muted">No products added — click "Add Product"</td></tr>';
        recalcTotals();
        return;
    }
    
    let html = "";
    currentCart.forEach((item, idx) => {
        const subtotal = item.price * item.quantity;
        html += `<tr>
                    <td>${escapeHtml(item.name)}<br><small class="text-muted">${escapeHtml(item.sku)}</small></td>
                    <td>₱${item.price.toFixed(2)}</td>
                    <td>
                        <input type="number" min="1" max="999" value="${item.quantity}" data-index="${idx}" class="cart-qty-input form-control form-control-sm" style="width:80px">
                    </td>
                    <td>₱${subtotal.toFixed(2)}</td>
                    <td><button class="btn btn-sm btn-danger remove-cart-item" data-index="${idx}"><i class="fas fa-trash"></i></button></td>
                 </tr>`;
    });
    tbody.innerHTML = html;
    
    // Quantity change events
    document.querySelectorAll(".cart-qty-input").forEach(input => {
        input.addEventListener("change", (e) => {
            const idx = parseInt(e.target.dataset.index);
            let newQty = parseInt(e.target.value);
            if (isNaN(newQty) || newQty < 1) newQty = 1;
            const product = masterProducts.find(p => p.id == currentCart[idx].productId);
            if (product && newQty > product.stock) {
                showNotification(`⚠️ Only ${product.stock} available`, 'error');
                newQty = product.stock;
                e.target.value = newQty;
            }
            currentCart[idx].quantity = newQty;
            renderCartTable();
            recalcTotals();
        });
    });
    
    // Remove events
    document.querySelectorAll(".remove-cart-item").forEach(btn => {
        btn.addEventListener("click", (e) => {
            const idx = parseInt(btn.dataset.index);
            currentCart.splice(idx, 1);
            renderCartTable();
            recalcTotals();
        });
    });
    
    recalcTotals();
}

// Recalculate totals
function recalcTotals() {
    let subtotal = currentCart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    let discount = parseFloat(document.getElementById("discountAmount")?.value) || 0;
    let totalAfterDiscount = Math.max(0, subtotal - discount);
    let amountPaid = parseFloat(document.getElementById("amountPaid")?.value) || 0;
    let changeDue = Math.max(0, amountPaid - totalAfterDiscount);
    
    const changeInput = document.getElementById("changeDue");
    if (changeInput) changeInput.value = changeDue.toFixed(2);
}

// Save sale
async function saveCurrentSale() {
    if (currentCart.length === 0) {
        showNotification("❌ Cannot save empty sale", 'error');
        return;
    }
    
    const subtotal = currentCart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
    const discount = parseFloat(document.getElementById("discountAmount")?.value) || 0;
    const total = Math.max(0, subtotal - discount);
    const amountPaid = parseFloat(document.getElementById("amountPaid")?.value) || 0;
    
    if (amountPaid < total) {
        showNotification(`⚠️ Insufficient payment`, 'error');
        return;
    }
    
    const saleData = {
        items: currentCart.map(item => ({
            product_id: item.productId,
            quantity: item.quantity,
            price: item.price
        })),
        subtotal: subtotal,
        discount: discount,
        total: total,
        amount_paid: amountPaid,
        change: amountPaid - total,
        notes: document.getElementById("saleNotes")?.value || "",
        payment_method: "cash"
    };
    
    try {
        const response = await fetch(baseUrl + 'sales/saveSale', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(saleData)
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            // Update stock locally
            for (let item of currentCart) {
                const product = masterProducts.find(p => p.id == item.productId);
                if (product) product.stock -= item.quantity;
            }
            
            currentCart = [];
            if (document.getElementById("discountAmount")) document.getElementById("discountAmount").value = "0";
            if (document.getElementById("amountPaid")) document.getElementById("amountPaid").value = "0";
            if (document.getElementById("saleNotes")) document.getElementById("saleNotes").value = "";
            
            renderCartTable();
            renderInventoryPanel();
            
            showNotification(`✅ Sale ${result.order_number} completed!`, 'success');
            
            // Reload sales history
            loadSalesHistory();
        } else {
            showNotification(`❌ Error: ${result.message}`, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification(`❌ Network error`, 'error');
    }
}

// Cancel sale
function cancelSale() {
    if (currentCart.length > 0 && confirm("Cancel current sale?")) {
        currentCart = [];
        document.getElementById("discountAmount").value = "0";
        document.getElementById("amountPaid").value = "0";
        document.getElementById("saleNotes").value = "";
        renderCartTable();
        showNotification("Sale cancelled", 'info');
    }
}

// Load sales history
async function loadSalesHistory() {
    try {
        const response = await fetch(baseUrl + 'sales/getSalesHistoryJson', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const result = await response.json();
        
        const tbody = document.getElementById("salesHistoryBody");
        if (!tbody) return;
        
        const sales = result.data || [];
        
        if (sales.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No recent sales</td></tr>';
            return;
        }
        
        let html = '';
        sales.slice(0, 10).forEach(sale => {
            html += `<tr>
                        <td><small>${escapeHtml(sale.order_number)}</small></td>
                        <td><small>${sale.created_at || '-'}</small></td>
                        <td><small>${sale.item_count || 0}</small></td>
                        <td><small>₱${parseFloat(sale.total || 0).toFixed(2)}</small></td>
                        <td><span class="badge badge-success">Completed</span></td>
                     </tr>`;
        });
        tbody.innerHTML = html;
    } catch (error) {
        console.error('Error loading sales:', error);
    }
}

// Add this function to get CSRF token
function getCsrfToken() {
    let token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!token) {
        token = document.querySelector('input[name="csrf_test_name"]')?.value;
    }
    return token;
}

// Update your saveSale function
async function completeSale() {
    if (cart.length === 0) {
        showNotification('Cart is empty!', 'error');
        return;
    }
    
    let subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    let discount = parseFloat(document.getElementById('discount')?.value) || 0;
    let total = subtotal - discount;
    let amountPaid = parseFloat(document.getElementById('amountPaid')?.value) || 0;
    
    if (amountPaid < total) {
        showNotification(`Insufficient payment! Need ₱${total.toFixed(2)}`, 'error');
        return;
    }
    
    const saleData = {
        items: cart.map(item => ({
            product_id: item.id,
            quantity: item.quantity,
            price: item.price
        })),
        subtotal: subtotal,
        discount: discount,
        total: total,
        amount_paid: amountPaid,
        change: amountPaid - total,
        notes: document.getElementById('notes')?.value || '',
        payment_method: 'cash',
        csrf_test_name: getCsrfToken()  // Add CSRF token here
    };
    
    try {
        const response = await fetch(baseUrl + 'sales/saveSale', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken()  // Also as header
            },
            body: JSON.stringify(saleData)
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            // Update local stock
            cart.forEach(item => {
                const product = products.find(p => p.id === item.id);
                if (product) product.stock -= item.quantity;
            });
            
            // Clear cart
            cart = [];
            document.getElementById('discount').value = '0';
            document.getElementById('amountPaid').value = '0';
            document.getElementById('notes').value = '';
            
            renderCart();
            displayProductList();
            loadSalesHistory();
            
            showNotification(`✅ Sale ${result.order_number} completed!`, 'success');
        } else {
            showNotification(`❌ Error: ${result.message}`, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Network error!', 'error');
    }
}

// Generate report
function generateReport() {
    const reportDiv = document.getElementById("reportPreview");
    if (reportDiv) {
        const totalSales = currentCart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
        reportDiv.innerHTML = `<strong>📋 Current Session</strong><br>
                               Items in cart: ${currentCart.length}<br>
                               Subtotal: ₱${currentCart.reduce((sum, i) => sum + (i.price * i.quantity), 0).toFixed(2)}<br>
                               Time: ${new Date().toLocaleTimeString()}`;
        showNotification("Report generated", 'success');
    }
}

// Export sales
function exportSalesData() {
    window.location.href = baseUrl + 'sales/export/csv';
    showNotification("Export started", 'success');
}

// Helper function
function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

// Update date/time
function updateDateTime() {
    const now = new Date();
    const elem = document.getElementById("saleDateTimeDisplay");
    if (elem) {
        elem.innerText = `${now.toLocaleDateString()} ${now.toLocaleTimeString()}`;
    }
}

// Initialize on page load
$(document).ready(function() {
    updateDateTime();
    setInterval(updateDateTime, 1000);
    
    loadProducts();
    loadSalesHistory();
    
    $("#addProductToSaleBtn").click(function() {
        loadProductSelector();
        $('#selectProductModal').modal('show');
    });
    
    $("#saveSaleBtn").click(saveCurrentSale);
    $("#cancelSaleBtn").click(cancelSale);
    $("#generateReportBtn").click(generateReport);
    $("#exportSalesBtn").click(exportSalesData);
    
    $("#discountAmount, #amountPaid").on('input', function() {
        recalcTotals();
    });
});
</script>
<?= $this->endSection() ?>