<?php
// 1. Ambil config (Keluar satu folder dulu untuk cari folder config)
if (file_exists('../config/config.php')) {
    include_once '../config/config.php';
}

// 2. Pastikan session dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. FAIL-SAFE SESSION (Penting: agar tidak muncul Fatal Error TypeError)
$is_logged_in = false;
$user_nama = 'User';
$user_uname = 'Guest';

if (isset($_SESSION) && is_array($_SESSION) && isset($_SESSION['user_id'])) {
    $is_logged_in = true;
    $user_nama = isset($_SESSION['nama_lengkap']) ? $_SESSION['nama_lengkap'] : 'User';
    $user_uname = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';
}

// 4. Base URL agar path CSS/JS tidak pecah
$base = (defined('BASE_URL')) ? BASE_URL : 'http://localhost/bandingin/';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Banding.in — Cari & Bandingkan Harga</title>
  <link rel="icon" href="<?= $base ?>public/images/logo-b.png" type="image/png">
  <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet"/>
  
  <link rel="stylesheet" href="<?= $base ?>public/css/list.css"/>
  
  <style>
    .favorite-nav-btn {
      background: linear-gradient(135deg, #ff6b6b, #ee5a5a) !important;
      color: white !important;
      border: none !important;
    }
    .favorite-nav-btn:hover {
      background: linear-gradient(135deg, #ff5252, #e04444) !important;
      transform: translateY(-1px);
    }
  </style>
</head>
<body>

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

  <nav>
    <span class="nav-brand" onclick="backToSearch()">
      banding<em style="font-family:'DM Serif Display',serif;font-style:italic">.in</em>
    </span>
    <div class="nav-links" id="navLinks">
      
      <?php if($is_logged_in) : ?> 
        <?php if(isSeller()) : ?>
        <button class="nav-btn"
                style="background: linear-gradient(135deg, #2ecad0, #2d5a9e) !important; color: white !important; border: none !important;"
                onclick="window.location.href='<?= $base ?>seller/add'">
          <i class="fa-solid fa-plus-circle"></i> <?= __('add_product') ?>
        </button>
        <?php elseif(isset($_SESSION['role']) && $_SESSION['role'] === 'user') : ?>
        <button class="nav-btn favorite-nav-btn"
                onclick="window.location.href='<?= $base ?>favorit'">
          ❤️ <?= __('favorite') ?>
        </button>
        <?php endif; ?>

        <div class="user-chip-wrap" id="userChipWrap">
          <div class="user-chip" onclick="toggleDropdown()">
            <div class="user-avatar" id="userAvatar" data-avatar="<?= htmlspecialchars($user_nama) ?>"></div>
            <span class="user-name"><?= htmlspecialchars($user_nama) ?></span>
            <div class="user-online"></div>
            <span class="user-chevron">▼</span>
          </div>
          <div class="user-dropdown">
            <div class="dropdown-info">
              <div class="dropdown-info-name"><?= htmlspecialchars($user_uname) ?></div>
              <div class="dropdown-info-label">Sedang login ✓</div>
            </div>
            <a class="dropdown-item" href="<?= $base ?>profile">
              <span class="dropdown-icon">👤</span> <?= __('my_profile') ?>
            </a>
            <?php if(isSuperAdmin()): ?>
            <a class="dropdown-item" href="<?= $base ?>admin/dashboard">
              <span class="dropdown-icon">🛡️</span> <?= __('admin_panel') ?>
            </a>
            <?php endif; ?>
            <div class="dropdown-item logout" onclick="doLogout()">
              <span class="dropdown-icon">🚪</span> <?= __('logout') ?>
            </div>
          </div>
        </div>
      <?php else: ?>
        <button class="nav-btn" onclick="window.location.href='<?= $base ?>login'"><?= __('login') ?></button>
      <?php endif; ?>
      <button class="nav-btn" onclick="window.location.href='<?= $base ?>aboutus'"><?= __('about_us') ?></button>
      
      <?php 
        $currentLang = $_SESSION['lang'] ?? 'en';
        $nextLang = $currentLang === 'en' ? 'id' : 'en';
        $flagImg = $currentLang === 'en' ? 'https://flagcdn.com/w40/us.png' : 'https://flagcdn.com/w40/id.png';
      ?>
      <button class="nav-btn" style="border-radius: 50%; padding: 0; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);" onclick="window.location.href='<?= $base ?>lang/switch?lang=<?= $nextLang ?>'" title="Switch Language">
        <img src="<?= $flagImg ?>" alt="flag" style="width: 20px; height: 20px; border-radius: 50%; object-fit: cover;">
      </button>

    </div>
  </nav>

  <div class="page" id="search">
    <div class="search-wrapper">
      <img src="<?= $base ?>public/images/logo-b.png" alt="Banding.in" class="search-logo" />
      <div class="search-box">
        <input class="search-input" id="searchInput" type="text" placeholder="<?= __('search_placeholder') ?>"/>
        <button class="search-btn" onclick="doSearch()"><?= __('compare') ?></button>
      </div>

      <div class="platform-filter">
        <button class="pf-btn active" data-platform="all"       onclick="togglePlatform(this)"><span class="pf-dot" style="background:#888"></span> <?= __('all') ?></button>
        <button class="pf-btn active" data-platform="tokopedia" onclick="togglePlatform(this)"><span class="pf-dot" style="background:#42b549"></span> Tokopedia</button>
        <button class="pf-btn active" data-platform="lazada"    onclick="togglePlatform(this)"><span class="pf-dot" style="background:#0f146b"></span> Lazada</button>
        <button class="pf-btn active" data-platform="blibli"    onclick="togglePlatform(this)"><span class="pf-dot" style="background:#0095d9"></span> Blibli</button>
      </div>

      <div class="most-searched" id="mostSearched">
        <div class="most-searched-label">🔥 <?= __('most_searched') ?></div>
        <div class="most-searched-tags">
          <span class="ms-tag" onclick="fillSearch('iPhone 15')"><span class="ms-tag-icon">📱</span> iPhone 15</span>
          <span class="ms-tag" onclick="fillSearch('Samsung Galaxy S24')"><span class="ms-tag-icon">📱</span> Samsung Galaxy S24</span>
          <span class="ms-tag" onclick="fillSearch('Nike Air Max')"><span class="ms-tag-icon">👟</span> Nike Air Max</span>
          <span class="ms-tag" onclick="fillSearch('AirPods Pro')"><span class="ms-tag-icon">🎧</span> AirPods Pro</span>
          <span class="ms-tag" onclick="fillSearch('Xiaomi Redmi')"><span class="ms-tag-icon">📱</span> Xiaomi Redmi</span>
          <span class="ms-tag" onclick="fillSearch('Tas Ransel')"><span class="ms-tag-icon">🎒</span> Tas Ransel</span>
          <span class="ms-tag" onclick="fillSearch('PS5')"><span class="ms-tag-icon">🎮</span> PS5 Slim</span>
        </div>
      </div>

      <div class="results-area" id="resultsArea" style="display:none;"></div>
    </div>
  </div>

  <div class="page hidden" id="listing">
    <div class="listing-shell">
      <aside class="ls-sidebar">
        <div class="ls-panel">
          <div class="ls-panel-title"><?= __('platform') ?></div>
          <div class="ls-platform-row on" data-pf="tokopedia" onclick="lsTogglePf(this)">
            <div class="ls-pf-dot" style="background:#42b549"></div> Tokopedia
            <div class="ls-check"><svg class="ls-check-icon" width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
          </div>
          <div class="ls-platform-row on" data-pf="lazada" onclick="lsTogglePf(this)">
            <div class="ls-pf-dot" style="background:#0f146b"></div> Lazada
            <div class="ls-check"><svg class="ls-check-icon" width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
          </div>
          <div class="ls-platform-row on" data-pf="blibli" onclick="lsTogglePf(this)">
            <div class="ls-pf-dot" style="background:#0095d9"></div> Blibli
            <div class="ls-check"><svg class="ls-check-icon" width="9" height="9" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
          </div>
        </div>

        <div class="ls-panel">
          <div class="ls-panel-title"><?= __('sort') ?></div>
          <div class="ls-sort-row on" data-sort="cheapest"  onclick="lsSetSort(this)"><span class="ls-sort-icon">↑</span> <?= __('cheapest') ?></div>
          <div class="ls-sort-row"    data-sort="expensive" onclick="lsSetSort(this)"><span class="ls-sort-icon">↓</span> <?= __('most_expensive') ?></div>
        </div>

        <div class="ls-panel">
          <div class="ls-panel-title"><?= __('price_range') ?></div>
          <div class="price-range-wrap">
            <div class="price-display">
              <div class="price-display-val" id="priceMinLabel">Rp 0</div>
              <div class="price-display-sep">—</div>
              <div class="price-display-val" id="priceMaxLabel">Rp 25jt</div>
            </div>
            <div class="slider-track-wrap">
              <div class="slider-track-bg"></div>
              <div class="slider-track-fill" id="sliderFill"></div>
              <input class="slider-range" id="sliderMin" type="range" min="0" max="25000000" step="100000" value="0" oninput="updateSlider()"/>
              <input class="slider-range" id="sliderMax" type="range" min="0" max="25000000" step="100000" value="25000000" oninput="updateSlider()"/>
            </div>
            <button class="apply-price-btn" onclick="lsApplyPrice()"><?= __('apply_filter') ?></button>
          </div>
        </div>
      </aside>

      <div class="ls-main" id="lsMain"></div>
    </div>
  </div>

  <div class="modal-overlay" id="loginModal" onclick="if(event.target===this) closeLoginModal()">
    <div class="modal-box">
      <button class="modal-close-x" onclick="closeLoginModal()">✕</button>
      <div class="modal-icon">❤️</div>
      <div class="modal-title"><?= __('save_to_favorite') ?></div>
      <div class="modal-sub"><?= __('favorite_login_hint') ?></div>
      <div class="modal-actions">
        <button class="modal-btn-login"    onclick="window.location.href='<?= $base ?>login'"><?= __('login') ?></button>
      </div>
      <button class="modal-close" onclick="closeLoginModal()"><?= __('continue_without_account') ?></button>
    </div>
  </div>

  <!-- Report Modal -->
  <div class="modal-overlay" id="reportModal" onclick="if(event.target===this) closeReportModal()">
    <div class="modal-box">
      <button class="modal-close-x" onclick="closeReportModal()">✕</button>
      <div class="modal-icon">🚩</div>
      <div class="modal-title">Laporkan Produk</div>
      <div class="modal-sub">Pilih alasan pelaporan produk ini:</div>
      <div class="report-options" style="text-align: left; margin-bottom: 15px; color: var(--text-light); font-size: 0.9rem;">
        <div style="margin-bottom: 8px;">
            <input type="radio" id="reason1" name="report_reason_radio" value="Barang tidak relevan" onchange="toggleReportReason(this)">
            <label for="reason1" style="cursor: pointer; padding-left: 5px;">Barang tidak relevan</label>
        </div>
        <div style="margin-bottom: 8px;">
            <input type="radio" id="reason2" name="report_reason_radio" value="Harga barang tidak sesuai" onchange="toggleReportReason(this)">
            <label for="reason2" style="cursor: pointer; padding-left: 5px;">Harga barang tidak sesuai</label>
        </div>
        <div style="margin-bottom: 8px;">
            <input type="radio" id="reason3" name="report_reason_radio" value="Barang sudah habis" onchange="toggleReportReason(this)">
            <label for="reason3" style="cursor: pointer; padding-left: 5px;">Barang sudah habis</label>
        </div>
        <div style="margin-bottom: 8px;">
            <input type="radio" id="reason4" name="report_reason_radio" value="other" onchange="toggleReportReason(this)">
            <label for="reason4" style="cursor: pointer; padding-left: 5px;">Lainnya (Isi sendiri)</label>
        </div>
        <textarea id="reportReasonText" style="width: 100%; display: none; margin-top: 10px; padding: 10px; border-radius: 8px; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.15); color: white; font-family: 'Lora', serif;" rows="3" placeholder="Tulis alasan Anda..."></textarea>
      </div>
      <div class="modal-actions">
        <button class="modal-btn-login" id="btnSubmitReport" onclick="submitReport()" style="background: linear-gradient(135deg, #e87d3e, #dc3545);">Ajukan Laporan</button>
      </div>
    </div>
  </div>

  <div class="toast" id="toast"></div>

  <script>
    const APP_IS_LOGGED_IN = <?= $is_logged_in ? 'true' : 'false' ?>;
    const IS_SELLER = <?= (isset($_SESSION['role']) && $_SESSION['role'] === 'seller') ? 'true' : 'false' ?>;
    const IS_SUPERADMIN = <?= (isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin') ? 'true' : 'false' ?>;
    
    // Translations for JS
    const LANG = {
        results_found: "<?= __('results_found') ?>",
        updated_just_now: "<?= __('updated_just_now') ?>",
        search_again: "<?= __('search_again') ?>",
        no_products_found: "<?= __('no_products_found') ?>",
        no_products_hint: "<?= __('no_products_hint') ?>",
        no_products_price: "<?= __('no_products_price') ?>",
        no_products_price_hint: "<?= __('no_products_price_hint') ?>",
        visit: "<?= __('visit') ?>",
        cheapest: "<?= __('cheapest') ?>",
        report: "Laporkan",
        submit_report: "Ajukan Laporan"
    };
  </script>
  <script src="<?= $base ?>public/js/list.js?v=<?= time(); ?>"></script>

</body>
</html>