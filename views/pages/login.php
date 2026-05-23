<?php 
$initialMode = isset($_GET['mode']) && $_GET['mode'] === 'seller' ? 'seller' : 'user';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - <?= APP_NAME ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="./public/images/logo-b.png" type="image/png">
  <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="./public/css/auth.css?v=<?= time() ?>">
</head>
<body>

  <div class="auth-wrapper">
    <!-- ROLE TOGGLE TABS -->
    <div class="role-toggle <?= $initialMode === 'seller' ? 'seller-active' : 'user-active' ?>" id="roleToggle">
      <button class="role-tab <?= $initialMode === 'user' ? 'active' : '' ?>" id="tabUser" data-role="user">
        <i class="fa-solid fa-user"></i> User
      </button>
      <button class="role-tab <?= $initialMode === 'seller' ? 'active' : '' ?>" id="tabSeller" data-role="seller">
        <i class="fa-solid fa-store"></i> Seller
      </button>
      <div class="role-tab-indicator" id="tabIndicator"></div>
    </div>

    <div class="container-wrapper">
      <!-- ==================== USER MODE ==================== -->
      <div class="container <?= $initialMode === 'user' ? '' : 'hidden-mode' ?>" id="container" data-mode="user" style="display: <?= $initialMode === 'user' ? 'block' : 'none' ?>;">

    <!-- USER LOGIN FORM -->
    <div class="form-container login-container" >
      <form id="loginForm" action="<?= BASE_URL . 'login' ?>" method="post">
        <div class="icon-circle">
          <i class="fa-solid fa-user"></i>
        </div>
        <h2>MY ACCOUNT</h2>

        <?php if (!empty($_SESSION['errors_messages']) && is_string($_SESSION['errors_messages']) && !isset($_GET['mode'])): ?>
          <div class="error-alert"><?= e($_SESSION['errors_messages']) ?></div>
          <?php unset($_SESSION['errors_messages']); ?>
        <?php endif; ?>

        <?php displayFlashMessage(); ?>

        <input type="text"
        class="form-control"
        id="username"
        name="username"
        value="<?= e($_POST['username'] ?? '') ?>"
        placeholder="Masukkan username atau email"
        required
        autofocus>
        <input type="password"
        class="form-control"
        id="password"
        name="password"
        placeholder="Masukkan password"
        required>

        <button type="submit" class="primary-btn">SIGN IN</button>
      </form>
    </div>

    <!-- USER REGISTER FORM -->
    <div class="form-container register-container" style="overflow-y: auto;">
      <form action="<?= BASE_URL . 'register' ?>" method="post">
        <h2 style="margin-top: 89px;">Create Account</h2>

        <input type="text" name="username" placeholder="Username" required>
        <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>


        <button type="submit" class="primary-btn">SIGN UP</button>

      
      </form>
    </div>

    <!-- USER OVERLAY -->
    <div class="overlay-container">
      <div class="overlay">

        <div class="overlay-panel left-panel">
          <h2>Welcome Back!</h2>
          <p>To keep connected with us please login with your personal info</p>
          <button class="ghost" id="login">Sign In</button>
        </div>

        <div class="overlay-panel right-panel">
          <h2>Hello, Friend!</h2>
          <p>Enter your personal details and start your journey</p>
          <button class="ghost" id="register">Sign Up</button>
        </div>

      </div>
    </div>

  </div>

      <!-- ==================== SELLER MODE ==================== -->
      <div class="container seller-mode <?= $initialMode === 'seller' ? '' : 'hidden-mode' ?>" id="containerSeller" data-mode="seller" style="display: <?= $initialMode === 'seller' ? 'block' : 'none' ?>;">

    <!-- SELLER LOGIN FORM -->
    <div class="form-container login-container">
      <form id="sellerLoginForm" action="<?= BASE_URL . 'seller/login' ?>" method="post">
        <div class="icon-circle seller-icon">
          <i class="fa-solid fa-store"></i>
        </div>
        <h2>SELLER LOGIN</h2>

        <?php if (!empty($_SESSION['errors_messages']) && is_string($_SESSION['errors_messages']) && isset($_GET['mode']) && $_GET['mode'] === 'seller'): ?>
          <div class="error-alert"><?= e($_SESSION['errors_messages']) ?></div>
          <?php unset($_SESSION['errors_messages']); ?>
        <?php endif; ?>

        <?php if (isset($_GET['mode']) && $_GET['mode'] === 'seller'): ?>
          <?php displayFlashMessage(); ?>
        <?php endif; ?>

        <input type="text"
        class="form-control"
        name="username"
        placeholder="Username atau email seller"
        required>
        <input type="password"
        class="form-control"
        name="password"
        placeholder="Password seller"
        required>

        <button type="submit" class="primary-btn seller-btn">SIGN IN</button>

        <a href="<?= BASE_URL ?>seller/benefits" class="benefits-link">
        </a>
      </form>
    </div>

    <!-- SELLER REGISTER FORM -->
    <div class="form-container register-container" style="overflow-y: auto;">
      <form action="<?= BASE_URL . 'seller/register' ?>" method="post">
        <h2 style="margin-top: 40px; margin-bottom: 10px;">Daftar Seller</h2>
        <input type="text" name="username" placeholder="Username" required>
        <input type="text" name="nama_lengkap" placeholder="Nama Toko" required>
        <input type="email" name="email" placeholder="Email Bisnis" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit" class="primary-btn seller-btn">DAFTAR SELLER</button>

      </form>
    </div>

    <!-- SELLER OVERLAY -->
    <div class="overlay-container">
      <div class="overlay seller-overlay">

        <div class="overlay-panel left-panel">
                  <div class="seller-benefits-box">
          <h4>Keuntungan bergabung dengan Banding.in</h4>
          <ul>
            <li><i class="fa-solid fa-circle-check"></i> Product yang unggul dalam kategori harga akan lebih menonjol di website ini</li>
            <li><i class="fa-solid fa-circle-check"></i> Product dan toko akan lebih mudah dicari dan dikenali</li>
            <li><i class="fa-solid fa-circle-check"></i> Akses edit harga ter-update</li>
          </ul>
        </div>
          <p>Login untuk mengelola produk dan toko Anda</p>
          <button class="ghost" id="sellerLogin">Sign In</button>
          <a href="<?= BASE_URL ?>seller/benefits" class="benefits-link">
            <i class="fa-solid fa-gift"></i> Keuntungan Menjadi Seller
          </a>
        </div>

        <div class="overlay-panel right-panel">
          <h2>Mulai Berjualan!</h2>
          <p>Daftarkan toko Anda dan jangkau jutaan pembeli</p>
          <button class="ghost" id="sellerRegister">Daftar Seller</button>
          <a href="<?= BASE_URL ?>seller/benefits" class="benefits-link-overlay">
          </a>
        </div>

      </div>
    </div>

      </div>
    </div>
  </div>

  <script src="./public/js/auth.js?v=<?= time() ?>"></script>
</body>
</html>