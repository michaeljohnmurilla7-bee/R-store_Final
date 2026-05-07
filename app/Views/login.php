<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Store Login | Retail Management</title>
  <!-- Google Fonts: Poppins + Source Sans Pro backup -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- Font Awesome 6 (free) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fontawesome/6.0.0-beta3/css/all.min.css">
  <!-- Custom CSS: modern store theme -->
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', 'Source Sans Pro', sans-serif;
      background: linear-gradient(135deg, #0f2b3d 0%, #1b4f3b 100%);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    /* animated background pattern (soft store vibe) */
    body::before {
      content: "";
      position: absolute;
      width: 200%;
      height: 200%;
      top: -50%;
      left: -50%;
      background: radial-gradient(circle, rgba(255,255,255,0.08) 2%, transparent 2.5%);
      background-size: 50px 50px;
      animation: subtleShift 40s linear infinite;
      pointer-events: none;
    }

    @keyframes subtleShift {
      0% { transform: translate(0,0) rotate(0deg); }
      100% { transform: translate(30px, 30px) rotate(3deg); }
    }

    /* main login card container */
    .login-box {
      width: 450px;
      max-width: 90%;
      z-index: 10;
      animation: fadeSlideUp 0.8s ease-out;
    }

    @keyframes fadeSlideUp {
      from {
        opacity: 0;
        transform: translateY(40px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .card {
      background: rgba(255, 255, 255, 0.97);
      backdrop-filter: blur(2px);
      border-radius: 32px;
      box-shadow: 0 25px 45px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255,255,255,0.2);
      overflow: hidden;
      transition: transform 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .login-card-body {
      padding: 2rem 2rem 2.2rem;
    }

    /* Store logo area + modern styling */
    .store-logo {
      text-align: center;
      margin-bottom: 1.2rem;
    }

    /* Green circle with R logo - updated */
    .store-icon {
      width: 85px;
      height: 85px;
      background: linear-gradient(145deg, #2c7a4d, #1e5a3a);
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 12px 20px rgba(0, 0, 0, 0.2);
      margin-bottom: 12px;
      transition: all 0.2s;
    }

    /* R letter styling inside the green circle */
    .r-letter {
      font-size: 52px;
      font-weight: 800;
      font-family: 'Poppins', sans-serif;
      color: #FFE0A3;
      text-shadow: 0 2px 4px rgba(0,0,0,0.2);
      letter-spacing: -2px;
    }

    .store-title {
      font-weight: 700;
      font-size: 1.8rem;
      letter-spacing: -0.5px;
      background: linear-gradient(120deg, #1f5e42, #d4a373);
      background-clip: text;
      -webkit-background-clip: text;
      color: transparent;
      margin-bottom: 0.25rem;
    }

    .login-box-msg {
      font-size: 0.95rem;
      font-weight: 500;
      color: #3a5e4c;
      border-bottom: 2px solid #e9ecef;
      display: inline-block;
      padding-bottom: 6px;
      margin-top: 4px;
    }

    /* elegant form fields */
    .input-group {
      margin-bottom: 1.5rem;
      border-radius: 60px;
      transition: all 0.2s;
      display: flex;
      align-items: stretch;
    }

    .form-control {
      border: 1.5px solid #e2e8f0;
      border-radius: 60px 0 0 60px;
      padding: 0.9rem 1.2rem;
      font-size: 0.95rem;
      font-weight: 500;
      background: #ffffff;
      transition: all 0.25s;
      box-shadow: none;
      width: 100%;
      outline: none;
    }

    .form-control:focus {
      border-color: #2c7a4d;
      box-shadow: 0 0 0 3px rgba(44, 122, 77, 0.2);
      outline: none;
    }

    .input-group-text {
      background: #ffffff;
      border: 1.5px solid #e2e8f0;
      border-left: none;
      border-radius: 0 60px 60px 0;
      padding: 0 1.2rem;
      color: #2c7a4d;
      font-size: 1.2rem;
      display: flex;
      align-items: center;
    }

    /* checkbox icheck modern style */
    .icheck-primary {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .icheck-primary input {
      width: 18px;
      height: 18px;
      accent-color: #2c7a4d;
      cursor: pointer;
      margin: 0;
    }

    .icheck-primary label {
      font-weight: 500;
      color: #2d3e2f;
      cursor: pointer;
      font-size: 0.9rem;
    }

    /* modern button */
    .btn-primary {
      background: linear-gradient(95deg, #1f5e42, #2c7a4d);
      border: none;
      border-radius: 60px;
      font-weight: 600;
      padding: 0.7rem 1rem;
      font-size: 0.95rem;
      transition: 0.2s;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      width: 100%;
      cursor: pointer;
      color: white;
    }

    .btn-primary:hover {
      background: linear-gradient(95deg, #194f36, #23653f);
      transform: scale(1.02);
      box-shadow: 0 8px 18px rgba(0, 0, 0, 0.15);
    }

    .btn-primary:disabled {
      background: #a0c4b0;
      transform: none;
      cursor: not-allowed;
    }

    /* alert custom styling */
    .alert-custom {
      border-radius: 60px;
      font-size: 0.85rem;
      font-weight: 500;
      padding: 0.8rem 1rem;
      margin-bottom: 1.5rem;
      text-align: center;
      backdrop-filter: blur(4px);
    }

    .alert-warning-store {
      background: #fff3e0;
      border-left: 5px solid #e67e22;
      color: #a4510c;
    }

    .alert-success-store {
      background: #e0f7e8;
      border-left: 5px solid #2c7a4d;
      color: #1e5a3a;
    }

    .alert-danger-store {
      background: #ffe6e5;
      border-left: 5px solid #c7362b;
      color: #a12218;
    }

    /* timer & lockout */
    #lockout-timer {
      font-weight: 700;
      background: #f0b27a30;
      padding: 2px 6px;
      border-radius: 50px;
      font-family: monospace;
    }

    /* row alignment */
    .row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 10px;
    }

    .col-7 {
      flex: 1;
    }

    .col-5 {
      min-width: 120px;
    }

    /* responsive adjustments */
    @media (max-width: 500px) {
      .login-card-body {
        padding: 1.6rem;
      }
      .store-icon {
        width: 70px;
        height: 70px;
      }
      .r-letter {
        font-size: 42px;
      }
      .store-title {
        font-size: 1.5rem;
      }
      .row {
        flex-direction: column;
        align-items: stretch;
      }
      .btn-primary {
        width: 100%;
      }
    }

    /* decorative ribbon effect */
    .card::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 6px;
      background: linear-gradient(90deg, #d4a373, #2c7a4d, #f4d03f);
    }

    .text-center {
      text-align: center;
    }

    .mt-4 {
      margin-top: 1.5rem;
    }

    .mb-3 {
      margin-bottom: 1rem;
    }

    .mb-4 {
      margin-bottom: 1.5rem;
    }

    .btn-block {
      display: block;
      width: 100%;
    }
  </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="card">
    <div class="card-body login-card-body">
      <!-- Store Branding : Logo with R inside green circle -->
      <div class="store-logo">
        <div class="store-icon">
          <span class="r-letter">R</span>
        </div>
        <div class="store-title">R<span style="font-weight:400">-store</span></div>
        <p class="login-box-msg">
          <i class="fas fa-chalkboard-user"></i>  Sign in to manage your Store
        </p>
      </div>

      <!-- lockout / error messages : enhanced styling -->
      <?php $lockoutTime = $lockout ?? 0; ?>

      <?php if ($lockoutTime > 0): ?>
        <div class="alert-custom alert-warning-store" id="lockout-alert">
          <i class="fas fa-clock me-2"></i> <strong>Too many login attempts.</strong><br>
          Please wait <span id="lockout-timer" style="font-weight:700;"></span> before trying again.
        </div>
      <?php elseif (session()->getFlashdata('error')): ?>
        <div class="alert-custom alert-danger-store">
          <i class="fas fa-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
        </div>
      <?php endif; ?>

      <!-- Login Form -->
      <form action="<?= base_url('/auth') ?>" method="post">
        <?= csrf_field() ?>

        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="Store Email address" required autocomplete="email">
          <div class="input-group-append">
            <div class="input-group-text"><i class="fas fa-envelope"></i></div>
          </div>
        </div>

        <div class="input-group mb-4">
          <input type="password" name="password" class="form-control" placeholder="Password" required autocomplete="current-password">
          <div class="input-group-append">
            <div class="input-group-text"><i class="fas fa-lock"></i></div>
          </div>
        </div>

        <div class="row align-items-center">
          <div class="col-7">
            <div class="icheck-primary">
              <input type="checkbox" id="remember" name="remember">
              <label for="remember">Keep me signed in</label>
            </div>
          </div>
          <div class="col-5">
            <button type="submit" class="btn-primary btn-block" id="signInBtn"
              <?= ($lockoutTime > 0) ? 'disabled' : '' ?>>
              <i class='fas fa-arrow-right-to-bracket'></i> Sign In
            </button>
          </div>
        </div>
        
        <!-- extra store hint (optional) -->
        <div class="text-center mt-4" style="font-size:12px; color:#7d8f7a;">
          <i class="fas fa-shield-alt"></i> Secure access for store administrators
        </div>
      </form>
    </div>
  </div>
</div>

<!-- scripts -->
<script src="<?= base_url('assets/adminlte/plugins/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/adminlte/dist/js/adminlte.min.js') ?>"></script>

<?php if ($lockoutTime > 0): ?>
<script>
  // Enhanced lockout countdown with store theme matching
  let secondsLeft = <?= (int)$lockoutTime ?>;
  const timerDisplay = document.getElementById('lockout-timer');
  const signInBtn = document.getElementById('signInBtn');
  const alertBox = document.getElementById('lockout-alert');
  
  function formatTime(secs) {
    let minutes = Math.floor(secs / 60);
    let seconds = secs % 60;
    return `${minutes}m ${seconds.toString().padStart(2,'0')}s`;
  }
  
  function updateTimer() {
    if (secondsLeft > 0) {
      timerDisplay.textContent = formatTime(secondsLeft);
      secondsLeft--;
      setTimeout(updateTimer, 1000);
    } else {
      if (signInBtn) signInBtn.disabled = false;
      if (alertBox) {
        alertBox.classList.remove('alert-warning-store');
        alertBox.classList.add('alert-success-store');
        alertBox.innerHTML = `<i class="fas fa-check-circle"></i> <strong>Lockout expired! You can now sign in.</strong>`;
      }
    }
  }
  
  updateTimer();
</script>
<?php endif; ?>

<!-- in case base_url is not defined from PHP but typical CI, we keep dynamic. additional micro interaction -->
<script>
  // improve focus visual
  document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('focus', function() {
      this.closest('.input-group')?.classList.add('focused');
    });
    input.addEventListener('blur', function() {
      this.closest('.input-group')?.classList.remove('focused');
    });
  });
</script>
</body>
</html>