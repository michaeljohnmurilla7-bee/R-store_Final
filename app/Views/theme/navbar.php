<nav class="main-header navbar navbar-expand navbar-warning" id="mainNavbar">

  <!-- Left: hamburger + breadcrumb -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link rstore-nav-icon" data-widget="pushmenu" href="#" role="button">
        <i class="fas fa-bars"></i>
      </a>
    </li>
    <li class="nav-item d-none d-sm-flex align-items-center ml-2">
      <span class="rstore-brand-label">
        <i class="fas fa-store mr-1" style="font-size:13px;opacity:.8;"></i>
        RStore
      </span>
    </li>
  </ul>

  <!-- Right: theme toggle + user dropdown -->
  <ul class="navbar-nav ml-auto align-items-center">

    <!-- Theme toggle -->
    <li class="nav-item">
      <a class="nav-link rstore-nav-icon" href="#" id="themeToggle" title="Toggle theme">
        <i class="fas fa-sun"></i>
      </a>
    </li>

    <!-- User dropdown -->
    <li class="nav-item dropdown">
      <a class="nav-link rstore-user-btn dropdown-toggle" href="#"
         id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="rstore-avatar" id="userAvatar">
          <?= strtoupper(substr(session()->get('email') ?? 'A', 0, 1)) ?>
        </span>
        <span class="rstore-email d-none d-md-inline">
          <?= esc(session()->get('email')) ?>
        </span>
      </a>
      <div class="dropdown-menu dropdown-menu-right rstore-dropdown" aria-labelledby="userDropdown">
        <div class="rstore-dropdown-header">
          <div class="rstore-dropdown-avatar">
            <?= strtoupper(substr(session()->get('email') ?? 'A', 0, 1)) ?>
          </div>
          <div>
            <div class="rstore-dropdown-name">Signed in as</div>
            <div class="rstore-dropdown-email"><?= esc(session()->get('email')) ?></div>
          </div>
        </div>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="<?= base_url('users') ?>">
          <i class="fas fa-user-cog mr-2"></i> My Profile
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item rstore-logout" href="<?= base_url('/logout') ?>">
          <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </a>
      </div>
    </li>

  </ul>
</nav>