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
                  <span class="badge badge-success ml-2" id="orderStatusBadge">Active</span>
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
                      <th width="120">Quantity</th>
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
              
              <!-- Cart Summary Row -->
              <div class="row mt-3 border-top pt-3">
                <div class="col-6">
                  <strong>Subtotal:</strong> ₱<span id="subtotalDisplay">0.00</span><br>
                  <strong>Total after discount:</strong> ₱<span id="totalDisplay">0.00</span>
                </div>
                <div class="col-6 text-right">
                  <button type="button" class="btn btn-danger" id="cancelSaleBtn">
                    <i class="fas fa-trash-alt"></i> Cancel Sale
                  </button>
                  <button type="button" class="btn btn-success ml-2" id="saveSaleBtn">
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
                     <th>Product</th>
                     <th>Date</th>
                     <th>Items</th>
                     <th>Total</th>
                     <th>Status</th>
                    </tr>
                  </thead>
                  <tbody id="salesHistoryBody">
                  <tr><td colspan="6" class="text-center text-muted">Loading recent sales...</td></tr>
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
                  <div class="col-6"><strong>⚡ Low Stock Alert:</strong></div>
                  <div class="col-6" id="lowStockAlert">-</div>
                </div>
                <div class="row mt-1">
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

<!-- Add Stock Modal -->
<div class="modal fade" id="addStockModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title"><i class="fas fa-archive"></i> Add Stock</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="stockProductId">
        <div class="form-group">
          <label>Product</label>
          <input type="text" id="stockProductName" class="form-control" readonly>
        </div>
        <div class="form-group">
          <label>Current Stock</label>
          <input type="text" id="stockCurrentQty" class="form-control" readonly>
        </div>
        <div class="form-group">
          <label>Add Quantity</label>
          <input type="number" id="stockAddQty" class="form-control" min="1" value="0">
        </div>
        <div class="form-group">
          <label>New Stock</label>
          <input type="text" id="stockNewQty" class="form-control" readonly>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" id="confirmAddStockBtn">Add Stock</button>
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
let refreshInterval = null;
let isLoadingProducts = false;

// ============ HELPER FUNCTIONS ============
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

function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

function updateDateTime() {
    const now = new Date();
    const elem = document.getElementById("saleDateTimeDisplay");
    if (elem) {
        elem.innerText = `${now.toLocaleDateString()} ${now.toLocaleTimeString()}`;
    }
}

function getCsrfToken() {
    let token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!token) {
        token = document.querySelector('input[name="csrf_test_name"]')?.value;
    }
    return token;
}

// ============ AUTO REFRESH FUNCTIONS ============
function startAutoRefresh() {
    if (refreshInterval) clearInterval(refreshInterval);
    refreshInterval = setInterval(function() {
        console.log('Auto-refreshing products at:', new Date().toLocaleTimeString());
        loadProducts(true); // Silent refresh (no notification)
    }, 30000); // Refresh every 30 seconds
}

function stopAutoRefresh() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
        refreshInterval = null;
    }
}

// ============ INVENTORY FUNCTIONS ============
async function loadProducts(silent = false) {
    // Prevent multiple simultaneous requests
    if (isLoadingProducts) {
        console.log('Already loading products, skipping...');
        return;
    }
    
    isLoadingProducts = true;
    
    try {
        if (!silent) {
            showNotification('Refreshing products...', 'info');
        }
        
        // Add timestamp to prevent caching
        const timestamp = new Date().getTime();
        const response = await fetch(baseUrl + 'sales/getProductsJson?t=' + timestamp, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Cache-Control': 'no-cache, no-store, must-revalidate',
                'Pragma': 'no-cache'
            }
        });
        const result = await response.json();
        
        if (result.status === 'success' && result.data) {
            masterProducts = result.data;
            console.log('Products loaded at:', new Date().toLocaleTimeString());
            console.log('Stock summary:', masterProducts.map(p => ({name: p.name, stock: p.stock})));
            
            renderInventoryPanel();
            loadProductSelector();
            
            if (!silent) {
                showNotification(`✅ Stock updated - ${masterProducts.length} products loaded`, 'success');
            }
        } else {
            console.error('Error loading products:', result.message);
            if (!silent) {
                showNotification('Error loading products: ' + (result.message || 'Unknown error'), 'error');
            }
        }
    } catch (error) {
        console.error('Network error:', error);
        if (!silent) {
            showNotification('Network error loading products', 'error');
        }
    } finally {
        isLoadingProducts = false;
    }
}

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
    
    // Low stock alert
    const lowStockProducts = masterProducts.filter(p => p.stock > 0 && p.stock <= (p.reorder_level || 10));
    const lowStockAlert = document.getElementById("lowStockAlert");
    if (lowStockProducts.length > 0) {
        lowStockAlert.innerHTML = `<span class="badge badge-warning">${lowStockProducts.length} products low on stock</span>`;
        lowStockAlert.title = lowStockProducts.map(p => `${p.name}: ${p.stock} left`).join(', ');
    } else {
        lowStockAlert.innerHTML = '<span class="badge badge-success">All stocks are good</span>';
    }
    
    let html = '<div class="list-group list-group-flush">';
    masterProducts.forEach(prod => {
        let stockBadge = '';
        let stockText = '';
        
        if (prod.stock <= 0) {
            stockBadge = 'badge-danger';
            stockText = 'OUT OF STOCK';
        } else if (prod.stock <= (prod.reorder_level || 10)) {
            stockBadge = 'badge-warning';
            stockText = `Low: ${prod.stock}`;
        } else {
            stockBadge = 'badge-success';
            stockText = `${prod.stock} units`;
        }
        
        html += `<div class="list-group-item p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${escapeHtml(prod.name)}</strong><br>
                            <small class="text-muted">₱${parseFloat(prod.price).toFixed(2)} | Stock: <span class="badge ${stockBadge}">${stockText}</span></small>
                        </div>
                        <div>
                            <button class="btn btn-xs btn-warning mr-1 add-stock-btn" data-id="${prod.id}" data-name="${escapeHtml(prod.name)}" data-stock="${prod.stock}">
                                <i class="fas fa-plus-circle"></i> Add
                            </button>
                            <button class="btn btn-xs btn-primary add-from-inventory" data-id="${prod.id}" ${prod.stock <= 0 ? 'disabled' : ''}>
                                <i class="fas fa-cart-plus"></i> Sell
                            </button>
                        </div>
                    </div>
                 </div>`;
    });
    html += '</div>';
    container.innerHTML = html;
    
    // Attach sell buttons
    document.querySelectorAll(".add-from-inventory").forEach(btn => {
        btn.addEventListener("click", (e) => {
            const prodId = parseInt(btn.getAttribute("data-id"));
            const product = masterProducts.find(p => p.id == prodId);
            if (product && product.stock > 0) {
                addProductToCart(product);
            } else if (product && product.stock <= 0) {
                showNotification(`❌ ${product.name} is out of stock! Please add stock first.`, 'error');
            }
        });
    });
    
    // Attach add stock buttons
    document.querySelectorAll(".add-stock-btn").forEach(btn => {
        btn.addEventListener("click", (e) => {
            const prodId = parseInt(btn.getAttribute("data-id"));
            const prodName = btn.getAttribute("data-name");
            const currentStock = parseInt(btn.getAttribute("data-stock"));
            showAddStockModal(prodId, prodName, currentStock);
        });
    });
}

function showAddStockModal(productId, productName, currentStock) {
    document.getElementById("stockProductId").value = productId;
    document.getElementById("stockProductName").value = productName;
    document.getElementById("stockCurrentQty").value = currentStock;
    document.getElementById("stockAddQty").value = 0;
    document.getElementById("stockNewQty").value = currentStock;
    
    // Remove old event listener and add new one
    const stockAddQty = document.getElementById("stockAddQty");
    const newStockInput = document.getElementById("stockNewQty");
    
    const updateNewStock = function() {
        const addQty = parseInt(this.value) || 0;
        const newStock = currentStock + addQty;
        newStockInput.value = newStock;
    };
    
    // Remove existing listener and add new one
    stockAddQty.removeEventListener('input', updateNewStock);
    stockAddQty.addEventListener('input', updateNewStock);
    
    $('#addStockModal').modal('show');
}

async function confirmAddStock() {
    const productId = document.getElementById("stockProductId").value;
    const productName = document.getElementById("stockProductName").value;
    const addQty = parseInt(document.getElementById("stockAddQty").value) || 0;
    
    if (addQty <= 0) {
        showNotification('Please enter a valid quantity to add', 'error');
        return;
    }
    
    try {
        const response = await fetch(baseUrl + 'sales/addStock', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Cache-Control': 'no-cache'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: addQty
            })
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            // Update local stock
            const product = masterProducts.find(p => p.id == parseInt(productId));
            if (product) {
                product.stock += addQty;
                console.log(`Stock updated locally: ${product.name} now has ${product.stock}`);
            }
            
            // Refresh all displays
            renderInventoryPanel();
            loadProductSelector();
            
            $('#addStockModal').modal('hide');
            showNotification(`✅ Added ${addQty} units to ${productName}. New stock: ${product ? product.stock : result.new_stock}`, 'success');
            
            // Force a full refresh from server to ensure consistency
            setTimeout(() => {
                loadProducts(true);
            }, 500);
        } else {
            showNotification(`❌ Error: ${result.message}`, 'error');
        }
    } catch (error) {
        console.error('Error adding stock:', error);
        showNotification('Network error while adding stock', 'error');
    }
}

function loadProductSelector() {
    const tbody = document.getElementById("productSelectorBody");
    const searchInput = document.getElementById("productSearchInput");
    if (!tbody) return;
    
    function renderFilteredProducts(filter = '') {
        const filtered = masterProducts.filter(p => 
            p.name.toLowerCase().includes(filter.toLowerCase()) || 
            (p.sku && p.sku.toLowerCase().includes(filter.toLowerCase()))
        );
        
        if (!filtered.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center">No products available</td></tr>';
            return;
        }
        
        let html = '';
        filtered.forEach(prod => {
            const stockStatus = prod.stock <= 0 ? 'danger' : (prod.stock <= (prod.reorder_level || 10) ? 'warning' : 'secondary');
            html += `<tr>
                        <td>${escapeHtml(prod.name)}</td>
                        <td><small>${escapeHtml(prod.sku)}</small></td>
                        <td>₱${parseFloat(prod.price).toFixed(2)}</td>
                        <td><span class="badge badge-${stockStatus}">${prod.stock}</span></td>
                        <td>
                            <button class="btn btn-sm btn-success select-product-btn" data-id="${prod.id}" ${prod.stock <= 0 ? 'disabled' : ''}>
                                <i class="fas fa-cart-plus"></i>
                            </button>
                        </td>
                     </tr>`;
        });
        tbody.innerHTML = html;
        
        document.querySelectorAll(".select-product-btn").forEach(btn => {
            btn.addEventListener("click", (e) => {
                const prodId = parseInt(btn.getAttribute("data-id"));
                const product = masterProducts.find(p => p.id == prodId);
                if (product && product.stock > 0) {
                    addProductToCart(product);
                    $('#selectProductModal').modal('hide');
                    showNotification(`Added ${product.name} to cart`, 'success');
                } else if (product && product.stock <= 0) {
                    showNotification(`❌ ${product.name} is out of stock!`, 'error');
                }
            });
        });
    }
    
    renderFilteredProducts('');
    
    if (searchInput) {
        // Remove old listener to avoid duplicates
        const newSearchInput = searchInput.cloneNode(true);
        searchInput.parentNode.replaceChild(newSearchInput, searchInput);
        
        newSearchInput.addEventListener('input', (e) => {
            renderFilteredProducts(e.target.value);
        });
    }
}

// ============ CART FUNCTIONS ============
function addProductToCart(product) {
    if (product.stock <= 0) {
        showNotification(`❌ ${product.name} is out of stock! Please add stock first.`, 'error');
        return false;
    }
    
    const existingItem = currentCart.find(item => item.productId == product.id);
    if (existingItem) {
        if (existingItem.quantity + 1 > product.stock) {
            showNotification(`⚠️ Cannot add more than ${product.stock} units of ${product.name}`, 'error');
            return false;
        }
        existingItem.quantity += 1;
    } else {
        currentCart.push({
            productId: product.id,
            name: product.name,
            sku: product.sku || '',
            price: parseFloat(product.price),
            quantity: 1
        });
    }
    renderCartTable();
    return true;
}

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
                showNotification(`⚠️ Only ${product.stock} units of ${product.name} available`, 'error');
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

function recalcTotals() {
    let subtotal = currentCart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    let discount = parseFloat(document.getElementById("discountAmount")?.value) || 0;
    let totalAfterDiscount = Math.max(0, subtotal - discount);
    let amountPaid = parseFloat(document.getElementById("amountPaid")?.value) || 0;
    let changeDue = Math.max(0, amountPaid - totalAfterDiscount);
    
    document.getElementById("subtotalDisplay").innerText = subtotal.toFixed(2);
    document.getElementById("totalDisplay").innerText = totalAfterDiscount.toFixed(2);
    
    const changeInput = document.getElementById("changeDue");
    if (changeInput) changeInput.value = changeDue.toFixed(2);
}

// ============ SALE FUNCTIONS ============
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
        showNotification(`⚠️ Insufficient payment. Need ₱${total.toFixed(2)}`, 'error');
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
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify(saleData)
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            // ✅ IMPORTANT: Do NOT deduct locally here!
            // Just refresh products from server to get the updated stock
            await loadProducts(true);  // Refresh from server
            
            // Clear cart and form
            currentCart = [];
            document.getElementById("discountAmount").value = "0";
            document.getElementById("amountPaid").value = "0";
            document.getElementById("saleNotes").value = "";
            
            renderCartTable();
            loadSalesHistory();
            
            showNotification(`✅ Sale ${result.order_number || 'completed'}! Stock updated.`, 'success');
        } else {
            showNotification(`❌ Error: ${result.message}`, 'error');
        }
    } catch (error) {
        console.error('Error saving sale:', error);
        showNotification(`❌ Network error - sale not saved`, 'error');
    }
}

function cancelSale() {
    if (currentCart.length > 0 && confirm("Cancel current sale? All items will be removed.")) {
        currentCart = [];
        document.getElementById("discountAmount").value = "0";
        document.getElementById("amountPaid").value = "0";
        document.getElementById("saleNotes").value = "";
        renderCartTable();
        showNotification("Sale cancelled", 'info');
    } else if (currentCart.length === 0) {
        showNotification("No active sale to cancel", 'info');
    }
}

async function loadSalesHistory() {
    console.log('🔵 loadSalesHistory() CALLED');
    try {
        // Add timestamp to prevent caching
        const timestamp = new Date().getTime();
        const response = await fetch(baseUrl + 'sales/getSalesHistoryJson?t=' + timestamp, {
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Cache-Control': 'no-cache, no-store, must-revalidate',
                'Pragma': 'no-cache'
            }
        });
        const result = await response.json();
        
        console.log('🔵 Sales API Response:', result);
        
        const tbody = document.getElementById("salesHistoryBody");
        if (!tbody) {
            console.log('🔵 Error: salesHistoryBody not found');
            return;
        }
        
        const sales = result.data || [];
        
        if (sales.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No recent sales</td></tr>';
            return;
        }
        
        let html = '';
        sales.slice(0, 10).forEach(sale => {
            const orderNumber = sale.order_number || 'N/A';
            const productName = sale.product_name || 'N/A';
            const date = sale.sale_date || '-';
            const items = sale.item_count || 0;
            const total = parseFloat(sale.total_amount || 0).toFixed(2);
            const status = sale.status || 'completed';
            
            html += `<tr>
                        <td><small>${escapeHtml(orderNumber)}</small></td>
                        <td><small>${escapeHtml(productName)}</small></td>
                        <td><small>${escapeHtml(date)}</small></td>
                        <td><small>${items}</small></td>
                        <td><small>₱${total}</small></td>
                        <td><span class="badge badge-success">${escapeHtml(status)}</span></td>
                     </tr>`;
        });
        tbody.innerHTML = html;
        console.log('🔵 Sales history displayed, count:', sales.length);
        
    } catch (error) {
        console.error('🔵 Error loading sales:', error);
        const tbody = document.getElementById("salesHistoryBody");
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Error loading sales</td></tr>';
        }
    }
}

// ============ REPORT FUNCTIONS ============
function generateReport() {
    const reportDiv = document.getElementById("reportPreview");
    if (reportDiv) {
        const totalItems = masterProducts.reduce((sum, p) => sum + (p.stock || 0), 0);
        const lowStockCount = masterProducts.filter(p => p.stock > 0 && p.stock <= (p.reorder_level || 10)).length;
        
        reportDiv.innerHTML = `<strong>📊 Store Summary</strong><br>
                               Total Products: ${masterProducts.length}<br>
                               Total Stock Units: ${totalItems}<br>
                               Low Stock Items: ${lowStockCount}<br>
                               ⏰ ${new Date().toLocaleString()}<br><br>
                               <em>Current cart contains ${currentCart.length} items</em>`;
        showNotification("Report generated", 'success');
    }
}

function exportSalesData() {
    window.location.href = baseUrl + 'sales/export/csv';
    showNotification("Export started", 'success');
}

// ============ PAGE VISIBILITY ============
// Refresh when page becomes visible again (coming back from Products page)
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        console.log('Page became visible - refreshing products');
        loadProducts(true);
        loadSalesHistory();
    }
});

// Also refresh when coming from Products page via back/forward
window.addEventListener('pageshow', function(event) {
    if (event.persisted || performance.getEntriesByType('navigation')[0].type === 'back_forward') {
        console.log('Page loaded from cache - refreshing products');
        loadProducts(true);
        loadSalesHistory();
    }
});

// ============ EVENT LISTENERS ============
// ============ EVENT LISTENERS ============
$(document).ready(function() {
    updateDateTime();
    setInterval(updateDateTime, 1000);
    
    // Initial load - load both products AND sales history
    loadProducts(false);
    loadSalesHistory();  // ← ADD THIS LINE - FIXES THE REFRESH ISSUE!
    
    // Start auto-refresh every 30 seconds for products
    startAutoRefresh();
    
    // Also auto-refresh sales history every 30 seconds
    setInterval(function() {
        console.log('Auto-refreshing sales history');
        loadSalesHistory();
    }, 30000);
    
    $("#addProductToSaleBtn").click(function() {
        loadProductSelector();
        $('#selectProductModal').modal('show');
    });
    
    $("#saveSaleBtn").click(saveCurrentSale);
    $("#cancelSaleBtn").click(cancelSale);
    $("#generateReportBtn").click(generateReport);
    $("#exportSalesBtn").click(exportSalesData);
    $("#confirmAddStockBtn").click(confirmAddStock);
    
    $("#discountAmount, #amountPaid").on('input', function() {
        recalcTotals();
    });
});

// Clean up on page unload
$(window).on('beforeunload', function() {
    stopAutoRefresh();
});
</script>
<?= $this->endSection() ?>