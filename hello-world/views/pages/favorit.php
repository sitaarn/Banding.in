<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Favorit Saya — Banding.in</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet"/>
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
      Banding<em style="font-family:'DM Serif Display',serif;font-style:italic">.in</em>
    </span>
    <div class="nav-links">
      <?php if(isLoggedIn()) : ?>

        <!-- Tombol Cari Produk -->
        <button class="nav-btn" onclick="window.location.href='<?= BASE_URL ?>list'">Cari Produk</button>

        <!-- User Chip dengan Dropdown -->
        <div class="user-chip-wrap" id="userChipWrap">
          <div class="user-chip" onclick="toggleDropdown()">
            <div class="user-avatar" id="userAvatar" data-avatar="<?= $_SESSION['name'] ?>"></div>
            <span class="user-name"><?= $_SESSION['name'] ?></span>
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
        <button class="nav-btn" onclick="window.location.href='<?= BASE_URL ?>aboutus'">About Us</button>

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
            <div class="fav-header-title">Favorit Saya</div>
            <div class="fav-header-sub">Produk yang kamu simpan</div>
          </div>
        </div>
        <div class="fav-header-stats">
          <div class="fav-stat">
            <div class="fav-stat-num" id="statTersimpan">0</div>
            <div class="fav-stat-lbl">Tersimpan</div>
          </div>
          <div class="fav-stat-divider"></div>
          <div class="fav-stat">
            <div class="fav-stat-num" id="statTermurah">0</div>
            <div class="fav-stat-lbl">Termurah</div>
          </div>
        </div>
      </div>

      <!-- Filter Bar -->
      <div class="fav-filterbar">
        <div class="fav-platform-filters">
          <span class="fav-filter-label">Platform:</span>
          <button class="fav-pf-btn active" data-pf="semua"     onclick="favTogglePf(this)">Semua</button>
          <button class="fav-pf-btn active" data-pf="tokopedia" onclick="favTogglePf(this)">Tokopedia</button>
          <button class="fav-pf-btn active" data-pf="shopee"    onclick="favTogglePf(this)">Shopee</button>
          <button class="fav-pf-btn active" data-pf="lazada"    onclick="favTogglePf(this)">Lazada</button>
          <button class="fav-pf-btn active" data-pf="blibli"    onclick="favTogglePf(this)">Blibli</button>
        </div>
        <div class="fav-filterbar-right">
          <select class="fav-sort-select" id="favSortSelect" onchange="favRender()">
            <option value="newest">Terbaru</option>
            <option value="cheapest">Termurah</option>
            <option value="expensive">Termahal</option>
            <option value="az">A → Z</option>
          </select>
          <button class="fav-hapus-semua-btn" onclick="favHapusSemua()">Hapus Semua</button>
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