<?= $this->extend('theme/template') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0"><b>DASHBOARD</b></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">  
            <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
            <li class="breadcrumb-item active">Dashboard v1</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <!-- Suppliers Card - UNIFIED GREEN -->
        <div class="col-lg-3 col-6">
          <div class="small-box bg-suppliers">
            <div class="inner">
              <h3>Suppliers</h3>
              <p>All Suppliers</p>
            </div>
            <div class="icon">
              <i class="fas fa-truck"></i>
            </div>
            <a href="<?= base_url('suppliers') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <!-- Categories Card - UNIFIED GREEN -->
        <div class="col-lg-3 col-6">
          <div class="small-box bg-categories">
            <div class="inner">
              <h3>Categories</h3>
              <p>All Categories</p>
            </div>
            <div class="icon">
              <i class="fas fa-tags"></i>
            </div>
            <a href="<?= base_url('categories') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <!-- Products Card - UNIFIED GREEN with dynamic count -->
        <div class="col-lg-3 col-6">
          <div class="small-box bg-products">
            <div class="inner">
              <h3 id="productCount">Product</h3>
              <p>Total Products</p>
            </div>
            <div class="icon">
              <i class="fas fa-boxes"></i>
            </div>
            <a href="<?= base_url('products') ?>" class="small-box-footer">View Products <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <!-- Sales Card - UNIFIED GREEN -->
        <div class="col-lg-3 col-6">
          <div class="small-box bg-sales">
            <div class="inner">
              <h3>Sales Orders</h3>
              <p>Sales Order</p>
            </div>
            <div class="icon">
              <i class="fas fa-chart-line"></i>
            </div>
            <a href="<?= base_url('sales') ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<style>
  /* =====================================================
     UNIFIED GREEN THEME FOR ALL CARDS
     ===================================================== */
  
  /* Remove old background colors */
  .small-box.bg-info,
  .small-box.bg-success,
  .small-box.bg-warning,
  .small-box.bg-danger {
    background: transparent !important;
  }
  
  /* Unified Green Gradient for all cards */
  .small-box.bg-suppliers,
  .small-box.bg-categories,
  .small-box.bg-products,
  .small-box.bg-sales {
    background: linear-gradient(135deg, #1a5f4b 0%, #0e3d30 100%) !important;
    border-radius: 16px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  
  /* Hover effect */
  .small-box.bg-suppliers:hover,
  .small-box.bg-categories:hover,
  .small-box.bg-products:hover,
  .small-box.bg-sales:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 28px rgba(26, 95, 75, 0.25);
  }
  
  /* White text for all inner content */
  .small-box.bg-suppliers .inner,
  .small-box.bg-categories .inner,
  .small-box.bg-products .inner,
  .small-box.bg-sales .inner,
  .small-box.bg-suppliers .inner h3,
  .small-box.bg-categories .inner h3,
  .small-box.bg-products .inner h3,
  .small-box.bg-sales .inner h3,
  .small-box.bg-suppliers .inner p,
  .small-box.bg-categories .inner p,
  .small-box.bg-products .inner p,
  .small-box.bg-sales .inner p,
  #productCount {
    color: white !important;
  }
  
  /* Icon styling */
  .small-box.bg-suppliers .icon,
  .small-box.bg-categories .icon,
  .small-box.bg-products .icon,
  .small-box.bg-sales .icon {
    color: rgba(255, 255, 255, 0.25);
    transition: transform 0.3s ease;
  }
  
  .small-box.bg-suppliers:hover .icon,
  .small-box.bg-categories:hover .icon,
  .small-box.bg-products:hover .icon,
  .small-box.bg-sales:hover .icon {
    transform: scale(1.05);
    color: rgba(255, 255, 255, 0.4);
  }
  
  /* Footer link styling */
  .small-box.bg-suppliers .small-box-footer,
  .small-box.bg-categories .small-box-footer,
  .small-box.bg-products .small-box-footer,
  .small-box.bg-sales .small-box-footer {
    background: rgba(0, 0, 0, 0.12);
    color: white !important;
    transition: background 0.3s ease, padding-left 0.3s ease;
  }
  
  .small-box.bg-suppliers .small-box-footer:hover,
  .small-box.bg-categories .small-box-footer:hover,
  .small-box.bg-products .small-box-footer:hover,
  .small-box.bg-sales .small-box-footer:hover {
    background: rgba(0, 0, 0, 0.2);
    padding-left: 1.2rem;
  }
  
  .small-box.bg-suppliers .small-box-footer i,
  .small-box.bg-categories .small-box-footer i,
  .small-box.bg-products .small-box-footer i,
  .small-box.bg-sales .small-box-footer i {
    color: white !important;
  }
  
  /* Product counter specific */
  #productCount {
    font-weight: 700;
    font-size: 2rem;
  }
  
  /* Loading spinner */
  .fa-spinner {
    color: white !important;
  }
  
  /* Breadcrumb green accent */
  .breadcrumb-item a {
    color: #1a5f4b;
  }
  .breadcrumb-item a:hover {
    color: #0e3d30;
  }
  .breadcrumb-item.active {
    color: #2c8b70;
  }
  
  /* Header styling */
  .content-header h1 {
    background: linear-gradient(135deg, #1a5f4b, #0e3d30);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
  }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Debug function to load product count - FULLY FUNCTIONAL
function loadProductCount() {
    var url = '<?= base_url("products/getCount") ?>';
    console.log('Fetching from URL:', url);
    
    // Show loading spinner
    const $countEl = $('#productCount');
    const currentText = $countEl.text();
    if (currentText === '—' || currentText === 'Error' || currentText.includes('Err')) {
        $countEl.html('<i class="fas fa-spinner fa-pulse" style="font-size:1.5rem; color: white;"></i>');
    }
    
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        timeout: 8000,
        success: function(response) {
            console.log('Success response:', response);
            let newCount = 0;
            if (response.count !== undefined && !isNaN(parseInt(response.count))) {
                newCount = parseInt(response.count);
            } else if (response.total !== undefined) {
                newCount = parseInt(response.total);
            } else if (typeof response === 'number') {
                newCount = response;
            } else {
                newCount = 0;
                console.warn('Unexpected response format');
            }
            
            // Smooth update with animation
            animateNumberUpdate($countEl, newCount);
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error Details:');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('Response Text:', xhr.responseText);
            console.error('Status Code:', xhr.status);
            
            let errorMsg = '⚠️ Err';
            if (xhr.status === 404) errorMsg = '404';
            else if (xhr.status === 500) errorMsg = '500';
            else errorMsg = '!';
            
            $('#productCount').html(errorMsg);
            $('#productCount').css('color', 'white');
            
            // Retry after 5 seconds
            setTimeout(function() {
                const currentErr = $('#productCount').text();
                if (currentErr === errorMsg || currentErr === '⚠️ Err' || currentErr === '404' || currentErr === '500' || currentErr === '!') {
                    console.log('Retrying product count fetch...');
                    loadProductCount();
                }
            }, 5000);
        }
    });
}

// Animate number update
function animateNumberUpdate($element, newValue) {
    const currentRaw = $element.text().replace(/[^0-9-]/g, '');
    let currentVal = parseInt(currentRaw);
    if (isNaN(currentVal)) currentVal = 0;
    
    $element.css('color', 'white');
    
    if (currentVal === newValue) {
        $element.addClass('count-update');
        setTimeout(function() { $element.removeClass('count-update'); }, 400);
        return;
    }
    
    const duration = 400;
    const startTime = performance.now();
    const startValue = currentVal;
    const endValue = newValue;
    const diff = endValue - startValue;
    
    function updateCounter(currentTime) {
        const elapsed = currentTime - startTime;
        let progress = Math.min(1, elapsed / duration);
        const easeProgress = 1 - (1 - progress) * (1 - progress);
        const interimValue = Math.floor(startValue + diff * easeProgress);
        $element.text(interimValue);
        $element.css('color', 'white');
        
        if (progress < 1) {
            requestAnimationFrame(updateCounter);
        } else {
            $element.text(endValue);
            $element.css('color', 'white');
            $element.addClass('count-update');
            setTimeout(function() { $element.removeClass('count-update'); }, 400);
        }
    }
    
    requestAnimationFrame(updateCounter);
}

// Add count-update animation style
$('head').append(`
  <style>
    .count-update {
      animation: gentlePop 0.35s cubic-bezier(0.2, 0.9, 0.4, 1.1);
    }
    @keyframes gentlePop {
      0% { transform: scale(1); opacity: 0.8; }
      50% { transform: scale(1.08); text-shadow: 0 2px 8px rgba(26, 95, 75, 0.4); }
      100% { transform: scale(1); opacity: 1; }
    }
  </style>
`);

// Load when page is ready
$(document).ready(function() {
    console.log('Document ready, loading product count with unified green theme...');
    loadProductCount();
    
    // Auto-refresh every 45 seconds
    setInterval(function() {
        console.log('Auto-refreshing product count...');
        loadProductCount();
    }, 45000);
});
</script>

<?= $this->endSection() ?>