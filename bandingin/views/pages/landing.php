<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Banding.in — Find Your Best Prices</title>
  <link rel="icon" href="./public/images/logo-b.png" type="image/png">
  <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet"/>
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
    <span class="nav-brand"></span>
    <div class="nav-links" id="navLinks">
      <?php if(isLoggedIn()) : ?>
        <div class="user-chip-wrap" id="userChipWrap">
          <div class="user-chip" onclick="toggleDropdown()">
            <div class="user-avatar" id="userAvatar" data-avatar="<?= $_SESSION['nama_lengkap'] ?>"><?= strtoupper(substr($_SESSION['nama_lengkap'] ?? '', 0, 2)) ?></div>
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
              <span class="dropdown-icon">👤</span> <?= __('my_profile') ?>
            </a>
            <?php if(isSuperAdmin()): ?>
            <a class="dropdown-item" href="<?= BASE_URL ?>admin/dashboard">
              <span class="dropdown-icon">🛡️</span> <?= __('admin_panel') ?>
            </a>
            <?php endif; ?>
            <div class="dropdown-item logout" onclick="doLogout()">
              <span class="dropdown-icon">🚪</span> <?= __('logout') ?>
            </div>
          </div>
        </div>
        <button class="nav-btn" onclick="window.location.href='<?= BASE_URL ?>aboutus'"><?= __('about_us') ?></button>
      <?php else: ?>
        <button class="nav-btn" onclick="goToLogin()"><?= __('login') ?></button>
        <button class="nav-btn" onclick="window.location.href='<?= BASE_URL ?>aboutus'"><?= __('about_us') ?></button>
      <?php endif; ?>

      <?php 
        $currentLang = $_SESSION['lang'] ?? 'en';
        $nextLang = $currentLang === 'en' ? 'id' : 'en';
        $flagImg = $currentLang === 'en' ? 'https://flagcdn.com/w40/us.png' : 'https://flagcdn.com/w40/id.png';
      ?>
      <button class="nav-btn" style="border-radius: 50%; padding: 0; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);" onclick="window.location.href='<?= BASE_URL ?>lang/switch?lang=<?= $nextLang ?>'" title="Switch Language">
        <img src="<?= $flagImg ?>" alt="flag" style="width: 20px; height: 20px; border-radius: 50%; object-fit: cover;">
      </button>

    </div>
  </nav>

  <div class="page" id="landing">
    <div class="landing-center">
      <div class="platform-pills">
        <span class="pill">Tokopedia</span>
        <span class="pill">Lazada</span>
        <span class="pill">Blibli</span>
      </div>

      <div class="main-card" onclick="goToSearch()">
        <img src="./public/images/logo-b.png" alt="Banding.in Logo" class="brand-logo" />
        <div class="brand-name">banding<em>.in</em></div>
      </div>



      <p class="tagline"><?= __('tagline') ?></p>

      <div class="stats-row">
        <div class="stat">
          <div class="stat-number">3</div>
          <div class="stat-label">PLATFORM</div>
        </div>
        <div class="stat-divider"></div>
        <div class="stat">
          <div class="stat-number">Easy</div>
          <div class="stat-label">To Use</div>
        </div>
      </div>
    </div>
  </div>

  <script src="./public/js/scriptlanding.js"></script>
</body>
</html>