<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Banding.in — Find Your Best Prices</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="./public/css/stylelanding.css">
</head>
<body>
  <div class="bg"></div>
  <div class="blobs">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
  </div>
  <div class="orbs">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="orb orb-4"></div>
    <div class="orb orb-5"></div>
    <div class="orb orb-6"></div>
    <div class="orb orb-7"></div>
  </div>
  <div class="grid-overlay"></div>

  <nav>
    <span class="nav-brand">Banding<em style="font-family:'DM Serif Display',serif;font-style:italic">.in</em></span>
    <div class="nav-links" id="navLinks">
      <?php if(isLoggedIn()) : ?>
        <div class="user-chip-wrap" id="userChipWrap">
          <div class="user-chip" onclick="toggleDropdown()">
            <div class="user-avatar" id="userAvatar" data-avatar="<?= $_SESSION['nama_lengkap'] ?>"></div>
            <span class="user-name"><?= $_SESSION['nama_lengkap'] ?></span>
            <div class="user-online"></div>
            <span class="user-chevron">▼</span>
          </div>
          <div class="user-dropdown">
            <div class="dropdown-info">

              <div class="dropdown-info-name"><?= $_SESSION['username'] ?></div>
              <div class="dropdown-info-label">Sedang login ✓</div>
            </div>
            <a class="dropdown-item" href="<?= BASE_URL . 'profile' ?>">
              <span class="dropdown-icon">👤</span> Profil Saya
            </a>
            <div class="dropdown-item logout" onclick="doLogout()">
              <span class="dropdown-icon">🚪</span> Logout
            </div>
          </div>
        </div>
        <button class="nav-btn" onclick="window.location.href='http://localhost/bandingin/aboutus'">About Us</button>

      <?php else: ?>
        <button class="nav-btn" onclick="goToLogin()">Login</button>
        <button class="nav-btn" onclick="window.location.href='http://localhost/bandingin/aboutus'">About Us</button>
      <?php endif; ?>


    </div>
  </nav>

  <div class="page" id="landing">
    <div class="landing-center">
      <div class="platform-pills">
        <span class="pill">Tokopedia</span>
        <span class="pill">Shopee</span>
        <span class="pill">Lazada</span>
        <span class="pill">Blibli</span>
      </div>

      <div class="main-card" onclick="goToSearch()">
        <div class="brand-name">Banding<em>.in</em></div>
      </div>




      <p class="tagline">find your best prices.</p>

      <div class="stats-row">
        <div class="stat">
          <div class="stat-number">4</div>
          <div class="stat-label">PLATFORM</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat">
          <div class="stat-number">Real-time</div>
          <div class="stat-label">PERBANDINGAN</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat">
          <div class="stat-number">Gratis</div>
          <div class="stat-label">SELAMANYA</div>
        </div>
      </div>
    </div>
  </div>

  <script src="./public/js/scriptlanding.js"></script>
</body>
</html>