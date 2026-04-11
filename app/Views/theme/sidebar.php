<aside class="main-sidebar sidebar-light-light elevation-4" id="mainSidebar">

  <!-- Brand -->
  <a href="<?= base_url('dashboard') ?>" class="brand-link rstore-brand" id="brandLink">
    <div class="rstore-brand-icon">
      <i class="fas fa-store"></i>
    </div>
    <span class="brand-text">
      <strong>RStore</strong>
      <small class="d-block" style="font-size:10px;opacity:.75;line-height:1;">Sales & Inventory</small>
    </span>
  </a>

  <div class="sidebar">

    <!-- User panel -->
    <div class="rstore-user-panel">
      <div class="rstore-user-panel-avatar">
        <?= strtoupper(substr(session()->get('email') ?? 'A', 0, 1)) ?>
      </div>
      <div class="rstore-user-panel-info">
        <div class="rstore-user-panel-name"><?= esc(session()->get('email')) ?></div>
        <span class="rstore-user-panel-badge">
          <i class="fas fa-circle" style="font-size:7px;"></i> Online
        </span>
      </div>
    </div>

    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column rstore-nav"
          data-widget="treeview" role="menu" data-accordion="false">

        <!-- MAIN -->
        <li class="nav-header rstore-nav-header">MAIN</li>

        <li class="nav-item rstore-nav-item">
          <a href="<?= base_url('dashboard') ?>" class="nav-link <?= is_active(1, 'dashboard') ?>">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <!-- INVENTORY -->
        <li class="nav-header rstore-nav-header">INVENTORY</li>

        <li class="nav-item rstore-nav-item">
          <a href="<?= base_url('products') ?>" class="nav-link <?= is_active(1, 'products') ?>">
            <i class="nav-icon fas fa-boxes"></i>
            <p>Products</p>
          </a>
        </li>

        <!-- CATEGORIES WITH SUBMENU (COLLAPSIBLE) -->
        <li class="nav-item rstore-nav-item has-treeview <?= is_active(2, 'categories') ? 'menu-open' : '' ?>">
          <a href="#" class="nav-link <?= is_active(2, 'categories') ?>">
            <i class="nav-icon fas fa-tags"></i>
            <p>
              Categories
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="<?= base_url('categories') ?>" class="nav-link <?= is_active(1, 'categories') ?>">
                <i class="fas fa-list nav-icon"></i>
                <p>List Categories</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?= base_url('categories/create') ?>" class="nav-link">
                <i class="fas fa-plus-circle nav-icon"></i>
                <p>Add Category</p>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item rstore-nav-item">
          <a href="<?= base_url('suppliers') ?>" class="nav-link <?= is_active(1, 'suppliers') ?>">
            <i class="nav-icon fas fa-truck"></i>
            <p>Suppliers</p>
          </a>
        </li>

        <!-- SALES -->
        <li class="nav-header rstore-nav-header">SALES</li>

        <li class="nav-item rstore-nav-item">
          <a href="<?= base_url('sales') ?>" class="nav-link <?= is_active(1, 'sales') ?>">
            <i class="nav-icon fas fa-shopping-cart"></i>
            <p>Sales Orders</p>
          </a>
        </li>

        <li class="nav-item rstore-nav-item">
          <a href="<?= base_url('customers') ?>" class="nav-link <?= is_active(1, 'customers') ?>">
            <i class="nav-icon fas fa-users"></i>
            <p>Customers</p>
          </a>
        </li>

        <!-- REPORTS -->
        <li class="nav-header rstore-nav-header">REPORTS</li>

        <li class="nav-item rstore-nav-item">
          <a href="<?= base_url('reports') ?>" class="nav-link <?= is_active(1, 'reports') ?>">
            <i class="nav-icon fas fa-chart-bar"></i>
            <p>Reports</p>
          </a>
        </li>

        <!-- SYSTEM -->
        <li class="nav-header rstore-nav-header">SYSTEM</li>

        <li class="nav-item rstore-nav-item">
          <a href="<?= base_url('log') ?>" class="nav-link <?= is_active(1, 'log') ?>">
            <i class="nav-icon fas fa-history"></i>
            <p>Activity Logs</p>
          </a>
        </li>

        <li class="nav-item rstore-nav-item">
          <a href="<?= base_url('users') ?>" class="nav-link <?= is_active(1, 'users') ?>">
            <i class="nav-icon fas fa-users-cog"></i>
            <p>User Management</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>

</aside>