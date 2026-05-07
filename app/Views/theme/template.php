<!DOCTYPE html>
<html lang="en" style="font-size: 14px;">
<head>
  <meta name="csrf-name" content="<?= csrf_token() ?>">
  <meta name="csrf-token" content="<?= csrf_hash() ?>">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>RStore — Dashboard | Green Theme</title>

  <!-- Fonts -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

  <!-- Icons -->
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

  <!-- AdminLTE Plugins -->
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/jqvmap/jqvmap.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/dist/css/adminlte.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/daterangepicker/daterangepicker.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/summernote/summernote-bs4.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/adminlte/plugins/toastr/toastr.min.css') ?>">

  <style>
    /* =====================================================
       RSTORE — GREEN DESIGN SYSTEM (matching Login Profile)
       ===================================================== */
    :root {
      --rs-green:        #1a5f4b;
      --rs-green-dark:   #0e3d30;
      --rs-green-light:  rgba(26, 95, 75, 0.12);
      --rs-green-glow:   rgba(26, 95, 75, 0.25);
      --rs-green-bright: #2c8b70;
      --rs-sidebar-bg:    #ffffff;
      --rs-sidebar-width: 250px;
      --rs-nav-header:    #9ca3af;
      --rs-text-muted:    #6b7280;
      --rs-border:        #e5e7eb;
      --rs-radius:        10px;
      --rs-shadow:        0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.05);
      --rs-shadow-md:     0 4px 12px rgba(0,0,0,.10);
      --rs-transition:    all 0.25s cubic-bezier(.4,0,.2,1);
    }

    /* ---- Font ---- */
    body, .nav-sidebar, .brand-text, .dropdown-item {
      font-family: 'DM Sans', 'Source Sans Pro', sans-serif !important;
    }

    /* =====================================================
       PAGE LOAD ANIMATION
       ===================================================== */
    @keyframes rs-fade-up {
      from { opacity: 0; transform: translateY(14px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes rs-fade-in {
      from { opacity: 0; }
      to   { opacity: 1; }
    }
    @keyframes rs-slide-in-left {
      from { opacity: 0; transform: translateX(-12px); }
      to   { opacity: 1; transform: translateX(0); }
    }

    .content-wrapper {
      animation: rs-fade-up 0.4s cubic-bezier(.4,0,.2,1) both;
    }

    /* =====================================================
       NAVBAR - GREEN GRADIENT (replaced orange)
       ===================================================== */
    .main-header.navbar-warning {
      background: linear-gradient(135deg, #1a5f4b 0%, #0e3d30 100%) !important;
      box-shadow: 0 2px 8px rgba(26, 95, 75, 0.35) !important;
      border-bottom: none !important;
    }

    /* Dark mode navbar */
    body.dark-mode .main-header {
      background: linear-gradient(135deg, #1f2937 0%, #111827 100%) !important;
      box-shadow: 0 2px 8px rgba(0,0,0,.4) !important;
    }

    .rstore-nav-icon {
      color: rgba(255,255,255,.9) !important;
      width: 38px;
      height: 38px;
      display: flex !important;
      align-items: center;
      justify-content: center;
      border-radius: 8px;
      transition: var(--rs-transition);
    }
    .rstore-nav-icon:hover {
      background: rgba(255,255,255,.15) !important;
      color: #fff !important;
    }

    .rstore-brand-label {
      color: rgba(255,255,255,.9);
      font-size: 13px;
      font-weight: 600;
      letter-spacing: .3px;
    }

    /* User button */
    .rstore-user-btn {
      display: flex !important;
      align-items: center;
      gap: 8px;
      padding: 4px 10px !important;
      border-radius: 20px;
      color: rgba(255,255,255,.95) !important;
      transition: var(--rs-transition);
    }
    .rstore-user-btn:hover {
      background: rgba(255,255,255,.15) !important;
    }
    .rstore-user-btn::after { display: none; }

    .rstore-avatar {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background: rgba(255,255,255,.25);
      border: 2px solid rgba(255,255,255,.5);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: 700;
      color: #fff;
      flex-shrink: 0;
    }

    .rstore-email {
      font-size: 13px;
      font-weight: 500;
      max-width: 160px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    /* Dropdown */
    .rstore-dropdown {
      border: 1px solid var(--rs-border);
      border-radius: var(--rs-radius);
      box-shadow: var(--rs-shadow-md);
      padding: 6px;
      min-width: 220px;
      animation: rs-fade-up 0.2s cubic-bezier(.4,0,.2,1) both;
    }

    body.dark-mode .rstore-dropdown {
      background: #1f2937;
      border-color: #374151;
    }

    .rstore-dropdown-header {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 10px 8px;
    }

    .rstore-dropdown-avatar {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: linear-gradient(135deg, #1a5f4b, #0e3d30);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      font-weight: 700;
      color: #fff;
      flex-shrink: 0;
    }

    .rstore-dropdown-name {
      font-size: 11px;
      color: var(--rs-text-muted);
      text-transform: uppercase;
      letter-spacing: .5px;
    }

    .rstore-dropdown-email {
      font-size: 13px;
      font-weight: 600;
      color: #111827;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 150px;
    }

    body.dark-mode .rstore-dropdown-email { color: #f9fafb; }

    .rstore-dropdown .dropdown-item {
      border-radius: 7px;
      padding: 8px 12px;
      font-size: 13px;
      font-weight: 500;
      color: #374151;
      transition: var(--rs-transition);
    }

    body.dark-mode .rstore-dropdown .dropdown-item { color: #d1d5db; }

    .rstore-dropdown .dropdown-item:hover {
      background: var(--rs-green-light);
      color: var(--rs-green-dark);
    }

    .rstore-logout:hover {
      background: rgba(239,68,68,.08) !important;
      color: #ef4444 !important;
    }

    /* =====================================================
       SIDEBAR - CLEAN WHITE / DARK SUPPORT
       ===================================================== */
    #mainSidebar {
      background: var(--rs-sidebar-bg) !important;
      border-right: 1px solid var(--rs-border) !important;
      box-shadow: 2px 0 12px rgba(0,0,0,.06) !important;
    }

    body.dark-mode #mainSidebar {
      background: #111827 !important;
      border-right-color: #1f2937 !important;
    }

    /* Brand - Green Gradient */
    .rstore-brand {
      display: flex !important;
      align-items: center;
      gap: 10px;
      padding: 14px 16px !important;
      background: linear-gradient(135deg, #1a5f4b 0%, #0e3d30 100%) !important;
      border-bottom: none !important;
      text-decoration: none !important;
    }

    body.dark-mode .rstore-brand {
      background: linear-gradient(135deg, #1f2937 0%, #111827 100%) !important;
    }

    .rstore-brand-icon {
      width: 36px;
      height: 36px;
      background: rgba(255,255,255,.2);
      border-radius: 9px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 16px;
      color: #fff;
      flex-shrink: 0;
      border: 1.5px solid rgba(255,255,255,.3);
    }

    .rstore-brand .brand-text {
      color: #fff !important;
      font-size: 15px !important;
      font-weight: 700 !important;
      line-height: 1.2;
    }

    /* User panel */
    .rstore-user-panel {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 14px 16px;
      border-bottom: 1px solid var(--rs-border);
      animation: rs-fade-in 0.4s ease both;
    }

    body.dark-mode .rstore-user-panel {
      border-bottom-color: #1f2937;
    }

    .rstore-user-panel-avatar {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      background: linear-gradient(135deg, #1a5f4b, #0e3d30);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 13px;
      font-weight: 700;
      color: #fff;
      flex-shrink: 0;
    }

    .rstore-user-panel-name {
      font-size: 12px;
      font-weight: 600;
      color: #111827;
      max-width: 150px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    body.dark-mode .rstore-user-panel-name { color: #f9fafb; }

    .rstore-user-panel-badge {
      font-size: 10px;
      color: #10b981;
      font-weight: 500;
    }

    /* Section headers */
    .rstore-nav-header {
      font-size: 10px !important;
      font-weight: 700 !important;
      letter-spacing: 1px !important;
      color: var(--rs-nav-header) !important;
      padding: 14px 16px 5px !important;
    }

    body.dark-mode .rstore-nav-header { color: #4b5563 !important; }

    /* Nav items */
    .rstore-nav-item {
      padding: 1px 8px !important;
    }

    /* Staggered entrance animation */
    .rstore-nav-item:nth-child(1)  { animation: rs-slide-in-left .3s .05s both; }
    .rstore-nav-item:nth-child(2)  { animation: rs-slide-in-left .3s .08s both; }
    .rstore-nav-item:nth-child(3)  { animation: rs-slide-in-left .3s .11s both; }
    .rstore-nav-item:nth-child(4)  { animation: rs-slide-in-left .3s .14s both; }
    .rstore-nav-item:nth-child(5)  { animation: rs-slide-in-left .3s .17s both; }
    .rstore-nav-item:nth-child(6)  { animation: rs-slide-in-left .3s .20s both; }
    .rstore-nav-item:nth-child(7)  { animation: rs-slide-in-left .3s .23s both; }
    .rstore-nav-item:nth-child(8)  { animation: rs-slide-in-left .3s .26s both; }
    .rstore-nav-item:nth-child(9)  { animation: rs-slide-in-left .3s .29s both; }
    .rstore-nav-item:nth-child(10) { animation: rs-slide-in-left .3s .32s both; }

    .rstore-nav .nav-link {
      border-radius: var(--rs-radius) !important;
      padding: 9px 12px !important;
      color: #374151 !important;
      font-weight: 500;
      font-size: 13.5px;
      transition: var(--rs-transition) !important;
      position: relative;
      overflow: hidden;
    }

    body.dark-mode .rstore-nav .nav-link { color: #d1d5db !important; }

    .rstore-nav .nav-link .nav-icon {
      font-size: 15px;
      width: 20px;
      color: var(--rs-text-muted) !important;
      transition: var(--rs-transition);
      margin-right: 10px !important;
    }

    body.dark-mode .rstore-nav .nav-link .nav-icon { color: #6b7280 !important; }

    /* Hover - Green */
    .rstore-nav .nav-link:hover {
      background: var(--rs-green-light) !important;
      color: var(--rs-green-dark) !important;
    }

    .rstore-nav .nav-link:hover .nav-icon {
      color: var(--rs-green) !important;
      transform: translateX(2px);
    }

    /* Active - Green */
    .rstore-nav .nav-link.active {
      background: var(--rs-green-light) !important;
      color: var(--rs-green-dark) !important;
      font-weight: 600 !important;
    }

    .rstore-nav .nav-link.active .nav-icon {
      color: var(--rs-green) !important;
    }

    /* Active left bar - Green */
    .rstore-nav .nav-link.active::before {
      content: "";
      position: absolute;
      left: 0; top: 20%; height: 60%;
      width: 3px;
      background: var(--rs-green);
      border-radius: 0 3px 3px 0;
    }

    body.dark-mode .rstore-nav .nav-link:hover,
    body.dark-mode .rstore-nav .nav-link.active {
      background: rgba(26, 95, 75, 0.12) !important;
      color: #2c8b70 !important;
    }

    body.dark-mode .rstore-nav .nav-link:hover .nav-icon,
    body.dark-mode .rstore-nav .nav-link.active .nav-icon {
      color: #2c8b70 !important;
    }

    /* =====================================================
       CONTENT AREA
       ===================================================== */
    .content-wrapper {
      background: #f9fafb !important;
    }

    body.dark-mode .content-wrapper {
      background: #0f172a !important;
    }

    /* Cards */
    .card {
      border: 1px solid var(--rs-border) !important;
      border-radius: var(--rs-radius) !important;
      box-shadow: var(--rs-shadow) !important;
      transition: var(--rs-transition);
    }

    .card:hover {
      box-shadow: var(--rs-shadow-md) !important;
      transform: translateY(-1px);
    }

    body.dark-mode .card {
      background: #1f2937 !important;
      border-color: #374151 !important;
    }

    .card-header {
      border-radius: calc(var(--rs-radius) - 1px) calc(var(--rs-radius) - 1px) 0 0 !important;
      border-bottom: 1px solid var(--rs-border) !important;
      font-weight: 600;
    }

    body.dark-mode .card-header {
      background: #111827 !important;
      border-bottom-color: #374151 !important;
      color: #f9fafb;
    }

    /* Buttons - Green Theme */
    .btn {
      border-radius: 8px !important;
      font-weight: 500 !important;
      font-size: 13px !important;
      transition: var(--rs-transition) !important;
    }

    .btn-warning, .btn-warning:hover {
      background: var(--rs-green) !important;
      border-color: var(--rs-green) !important;
      color: #fff !important;
    }

    .btn-warning:hover {
      background: var(--rs-green-dark) !important;
      border-color: var(--rs-green-dark) !important;
      transform: translateY(-1px);
      box-shadow: 0 4px 10px var(--rs-green-glow) !important;
    }

    /* Primary button override if needed */
    .btn-primary {
      background: #1a5f4b !important;
      border-color: #1a5f4b !important;
    }
    .btn-primary:hover {
      background: #0e3d30 !important;
      border-color: #0e3d30 !important;
    }

    /* =====================================================
       TOASTR
       ===================================================== */
    #toast-container > div {
      border-radius: 10px !important;
      box-shadow: 0 8px 24px rgba(0,0,0,.15) !important;
      font-family: 'DM Sans', sans-serif !important;
      font-size: 13.5px !important;
      padding: 12px 16px 12px 50px !important;
    }

    /* =====================================================
       DARK MODE — body level
       ===================================================== */
    body.dark-mode {
      background: #0f172a !important;
      color: #e2e8f0;
    }

    body.dark-mode .main-footer {
      background: #1f2937;
      border-top-color: #374151;
      color: #9ca3af;
    }

    body.dark-mode .dropdown-menu {
      background: #1f2937;
      border-color: #374151;
    }

    body.dark-mode .dropdown-divider {
      border-top-color: #374151;
    }

    /* =====================================================
       SCROLLBAR
       ===================================================== */
    .sidebar::-webkit-scrollbar { width: 4px; }
    .sidebar::-webkit-scrollbar-track { background: transparent; }
    .sidebar::-webkit-scrollbar-thumb {
      background: #e5e7eb;
      border-radius: 4px;
    }

    body.dark-mode .sidebar::-webkit-scrollbar-thumb { background: #374151; }

    /* =====================================================
       COLLAPSED SIDEBAR
       ===================================================== */
    .sidebar-collapse .rstore-user-panel,
    .sidebar-collapse .rstore-nav-header { opacity: 0; transition: opacity .2s; }

    /* =====================================================
       MODAL FIXES
       ===================================================== */
    .modal-backdrop {
      z-index: 1040 !important;
    }

    .modal {
      z-index: 1050 !important;
      overflow-y: auto !important;
    }

    .modal-open {
      overflow: auto !important;
      padding-right: 0 !important;
    }

    .modal-open .modal {
      overflow-x: hidden;
      overflow-y: auto;
    }

    body.modal-open {
      overflow: visible !important;
    }

    .modal-dialog {
      pointer-events: auto;
    }

    /* Footer link green */
    .main-footer span {
      color: #1a5f4b !important;
    }
    body.dark-mode .main-footer span {
      color: #2c8b70 !important;
    }
  </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <?= $this->include('theme/navbar') ?>
  <?= $this->include('theme/sidebar') ?>
  
  <!-- Main Content -->
  <?= $this->renderSection('content') ?>

  <footer class="main-footer" style="font-size:12px;">
    <strong>&copy; <?= date('Y') ?> <span style="color:var(--rs-green);">RStore</span></strong>
    &mdash; Sales &amp; Inventory System
    <div class="float-right d-none d-sm-inline-block text-muted">
      <b>Version</b> CI4.v1
    </div>
  </footer>

</div>

<!-- ===================================================== -->
<!-- SCRIPTS -->
<!-- ===================================================== -->
<script src="<?= base_url('assets/adminlte/plugins/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/jquery-ui/jquery-ui.min.js') ?>"></script>
<script> $.widget.bridge('uibutton', $.ui.button); </script>
<script src="<?= base_url('assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/chart.js/Chart.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/sparklines/sparkline.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/jqvmap/jquery.vmap.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/jquery-knob/jquery.knob.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/moment/moment.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/daterangepicker/daterangepicker.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/summernote/summernote-bs4.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/dist/js/adminlte.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/dist/js/pages/dashboard.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/jszip/jszip.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/pdfmake/pdfmake.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/pdfmake/vfs_fonts.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-buttons/js/buttons.html5.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-buttons/js/buttons.print.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/toastr/toastr.min.js') ?>"></script>

<script>
  // Toastr defaults
  toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: 'toast-bottom-right',
    timeOut: 3500,
    showMethod: 'fadeIn',
    hideMethod: 'fadeOut',
  };

  // Theme toggle functionality
  const themeToggle = document.getElementById('themeToggle');
  const navbar      = document.getElementById('mainNavbar');
  const sidebar     = document.getElementById('mainSidebar');
  const brandLink   = document.getElementById('brandLink');

  function applyTheme(dark) {
    if (dark) {
      document.body.classList.add('dark-mode');
      if (navbar) {
        navbar.classList.remove('navbar-warning');
        navbar.classList.add('navbar-dark', 'bg-dark');
      }
      if (themeToggle) themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
    } else {
      document.body.classList.remove('dark-mode');
      if (navbar) {
        navbar.classList.remove('navbar-dark', 'bg-dark');
        navbar.classList.add('navbar-warning');
      }
      if (themeToggle) themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
    }
  }

  // Apply saved theme on load
  const savedTheme = localStorage.getItem('rsTheme');
  applyTheme(savedTheme === 'dark');

  if (themeToggle) {
    themeToggle.addEventListener('click', function (e) {
      e.preventDefault();
      const isDark = document.body.classList.contains('dark-mode');
      applyTheme(!isDark);
      localStorage.setItem('rsTheme', isDark ? 'light' : 'dark');
    });
  }

  // Global modal cleanup to prevent backdrop issues
  $(document).ready(function() {
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open');
    
    $(document).on('hidden.bs.modal', '.modal', function() {
      $('.modal-backdrop').remove();
      $('body').removeClass('modal-open');
      $(this).removeData('bs.modal');
    });
  });
</script>

<?= $this->renderSection('scripts') ?>

</body>
</html>