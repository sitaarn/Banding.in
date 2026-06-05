<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= __('fav_my_favorites') ?> — Banding.in</title>
  <link rel="icon" href="<?= BASE_URL ?>public/images/favicon.png" type="image/png">
  <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/list.css"/>
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/favorit.css"/>
</head>
<body>

  <!-- Background layers (sama persis dengan list.php) -->
  <div class="bg"></div>
  <div class="blobs">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
  </div>
  <div class="orbs">
    <div class="orb orb-1"></div><div class="orb orb-2"></div><div class="orb orb-3"></div>
    <div class="orb orb-4"></div><div class="orb orb-5"></div><div class="orb orb-6"></div>
    <div class="orb orb-7"></div>
  </div>
  <div class="grid-overlay"></div>

  <!-- Navigation -->
  <nav>
    <span class="nav-brand" onclick="window.location.href='<?= BASE_URL ?>'">
      banding<em style="font-family:'DM Serif Display',serif;font-style:italic">.in</em>
    </span>
    <div class="nav-links">
      <?php if(isLoggedIn()) : ?>
        <button class="nav-btn" onclick="window.location.href='<?= BASE_URL ?>list'"><?= __('search_products') ?></button>
        
        <?php if(isSeller()) : ?>
        <button class="nav-btn"
                style="background: linear-gradient(135deg, #2ecad0, #2d5a9e) !important; color: white !important; border: none !important;"
                onclick="window.location.href='<?= BASE_URL ?>seller/add'">
          <i class="fa-solid fa-plus-circle"></i> <?= __('add_product') ?>
        </button>
        <?php elseif(isset($_SESSION['role']) && $_SESSION['role'] === 'user') : ?>
        <button class="nav-btn favorite-nav-btn"
                onclick="window.location.href='<?= BASE_URL ?>favorit'">
          ❤️ <?= __('favorite') ?>
        </button>
        <?php endif; ?>

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
              <div class="dropdown-info-label"><?= __('logged_in_status') ?></div>
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
              <span class="dropdown-icon"></span> <?= __('logout') ?>
            </div>
          </div>
        </div>

        <button class="nav-btn" onclick="window.location.href='<?= BASE_URL ?>aboutus'"><?= __('about_us') ?></button>

        <?php 
          $currentLang = $_SESSION['lang'] ?? 'en';
          $nextLang = $currentLang === 'en' ? 'id' : 'en';
          $flagImg = $currentLang === 'en' ? 'https://flagcdn.com/w40/us.png' : 'https://flagcdn.com/w40/id.png';
        ?>
        <button class="nav-btn" style="border-radius: 50%; padding: 0; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);" onclick="window.location.href='<?= BASE_URL ?>lang/switch?lang=<?= $nextLang ?>'" title="Switch Language">
          <img src="<?= $flagImg ?>" alt="flag" style="width: 20px; height: 20px; border-radius: 50%; object-fit: cover;">
        </button>

      <?php else: ?>
        <!-- Redirect ke login jika belum login -->
        <script>window.location.href = '<?= BASE_URL ?>login';</script>
      <?php endif; ?>
    </div>
  </nav>

  <!-- FAVORITE PAGE -->
  <div class="fav-page" id="favPage">
    <div class="fav-shell">

      <!-- Header Card -->
      <div class="fav-header-card">
        <div class="fav-header-left">
          <div class="fav-header-icon">🔖</div>
          <div class="fav-header-info">
            <div class="fav-header-title"><?= __('fav_my_favorites') ?></div>
            <div class="fav-header-sub"><?= __('fav_saved_products') ?></div>
          </div>
        </div>
        <div class="fav-header-stats">
          <div class="fav-stat">
            <div class="fav-stat-num" id="statTersimpan">0</div>
            <div class="fav-stat-lbl"><?= __('fav_saved') ?></div>
          </div>
          <div class="fav-stat-divider"></div>
          <div class="fav-stat">
            <div class="fav-stat-num" id="statTermurah">0</div>
            <div class="fav-stat-lbl"><?= __('fav_cheapest') ?></div>
          </div>
        </div>
      </div>

      <!-- Filter Bar -->
      <div class="fav-filterbar">
        <div class="fav-platform-filters">
          <span class="fav-filter-label">Platform:</span>
          <button class="fav-pf-btn active" data-pf="semua"     onclick="favTogglePf(this)"><?= __('fav_all') ?></button>
          <button class="fav-pf-btn active" data-pf="tokopedia" onclick="favTogglePf(this)">Tokopedia</button>
          <button class="fav-pf-btn active" data-pf="lazada"    onclick="favTogglePf(this)">Lazada</button>
          <button class="fav-pf-btn active" data-pf="blibli"    onclick="favTogglePf(this)">Blibli</button>
        </div>
        <div class="fav-filterbar-right">
          <select class="fav-sort-select" id="favSortSelect" onchange="favRender()">
            <option value="newest"><?= __('fav_newest') ?></option>
            <option value="cheapest"><?= __('fav_cheapest') ?></option>
            <option value="expensive"><?= __('fav_most_expensive') ?></option>
            <option value="az">A → Z</option>
          </select>
          <button class="fav-hapus-semua-btn" onclick="favHapusSemua()"><?= __('fav_delete_all') ?></button>
        </div>
      </div>

      <!-- List Area -->
      <div class="fav-list" id="favList">
        <!-- Diisi oleh JavaScript -->
      </div>

    </div>
  </div>

  <!-- Toast -->
  <div class="toast" id="toast"></div>

  <script src="<?= BASE_URL ?>public/js/favorit.js"></script>

</body>
</html>