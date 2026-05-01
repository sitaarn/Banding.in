<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Banding.in — Bandingkan Harga</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="./public/css/search.css">
  <style>
    /* Reset & Variabel */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --text-dark: #1e2d3a;
      --text-mid: #3a5068;
      --text-soft: #5a7a90;
      --blue: #4a7fc1;
      --teal: #5bbcb8;
    }
    html, body { height: 100%; font-family: 'DM Sans', sans-serif; overflow: hidden; }
    
  </style>
</head>
<body>
  <div class="bg"></div>
  <div class="blobs"><div class="blob blob-1"></div><div class="blob blob-2"></div><div class="blob blob-3"></div></div>
  <div class="orbs"><div class="orb orb-1"></div><div class="orb orb-2"></div><div class="orb orb-3"></div><div class="orb orb-4"></div><div class="orb orb-5"></div><div class="orb orb-6"></div><div class="orb orb-7"></div></div>
  <div class="grid-overlay"></div>

  <nav>
    <span class="nav-brand" onclick="window.location.href='http://localhost/bandingin/landing'">Banding<em>.in</em></span>
    <div class="nav-links" id="navLinks">
      <div class="nav-links" id="navLinks">
      <?php if(isLoggedIn()) : ?>
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
        <button class="nav-btn" onclick="window.location.href='http://localhost/bandingin/aboutus'">About Us</button>

      <?php else: ?>
        <button class="nav-btn" onclick="goToLogin()">Login</button>
        <button class="nav-btn" onclick="window.location.href='http://localhost/bandingin/aboutus'">About Us</button>
      <?php endif; ?>


    </div>
    </div>
  </nav>

  <div class="page" id="search">
    <div class="search-wrapper">
      <div class="search-brand" onclick="window.location.href='http://localhost/bandingin/landing'">Banding<em>.in</em></div>
      <div class="search-box">
        <input class="search-input" id="searchInput" type="text" placeholder="Cari produk..." />
        <button class="search-btn" onclick="doSearch()">Bandingkan</button>
      </div>
      <div class="platform-filter">
        <button class="pf-btn active" data-platform="all" onclick="togglePlatform(this)"><span class="pf-dot" style="background:#888"></span> Semua</button>
        <button class="pf-btn active" data-platform="tokopedia" onclick="togglePlatform(this)"><span class="pf-dot" style="background:#42b549"></span> Tokopedia</button>
        <button class="pf-btn active" data-platform="shopee" onclick="togglePlatform(this)"><span class="pf-dot" style="background:#ee4d2d"></span> Shopee</button>
        <button class="pf-btn active" data-platform="lazada" onclick="togglePlatform(this)"><span class="pf-dot" style="background:#0f146b"></span> Lazada</button>
        <button class="pf-btn active" data-platform="blibli" onclick="togglePlatform(this)"><span class="pf-dot" style="background:#0095d9"></span> Blibli</button>
      </div>
      <div class="most-searched" id="mostSearched">
        <div class="most-searched-label">🔥 Most Searched Product</div>
        <div class="most-searched-tags">
          <span class="ms-tag" onclick="goToListingPage('iPhone 15')"><span class="ms-tag-icon">📱</span> iPhone 15</span>
          <span class="ms-tag" onclick="goToListingPage('Samsung Galaxy S24')"><span class="ms-tag-icon">📱</span> Samsung Galaxy S24</span>
          <span class="ms-tag" onclick="goToListingPage('Nike Air Max')"><span class="ms-tag-icon">👟</span> Nike Air Max</span>
          <span class="ms-tag" onclick="goToListingPage('Laptop Asus')"><span class="ms-tag-icon">💻</span> Laptop Asus</span>
          <span class="ms-tag" onclick="goToListingPage('AirPods Pro')"><span class="ms-tag-icon">🎧</span> AirPods Pro</span>
          <span class="ms-tag" onclick="goToListingPage('Xiaomi Redmi')"><span class="ms-tag-icon">📱</span> Xiaomi Redmi</span>
          <span class="ms-tag" onclick="goToListingPage('Tas Ransel')"><span class="ms-tag-icon">🎒</span> Tas Ransel</span>
          <span class="ms-tag" onclick="goToListingPage('PS5')"><span class="ms-tag-icon">🎮</span> PS5</span>
        </div>
      </div>
      <div class="results-area" id="resultsArea" style="display:none;"></div>
    </div>
  </div>

  <script src="./public/js/search.js"></script>
</body>
</html>