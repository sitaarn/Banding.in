<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Banding.in — Profil Saya</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="./public/css/profile.css">
</head>

<body>

  <!-- Background layers -->
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

  <!-- Navigation -->
  <nav id="mainNav">
    <a class="nav-brand" href='http://localhost/hello-world/list'>Banding<em style="font-family:'DM Serif Display',serif;font-style:italic">.in</em></a>
    <div class="nav-links">
      <a class="nav-btn" href='http://localhost/hello-world/list'>Cari Produk</a>
      <a class="nav-btn" href="<?= BASE_URL ?>aboutus">About Us</a>
      <div class="user-chip-wrap" id="userChipWrap">
        <div class="user-chip" onclick="toggleDropdown()">
          <div class="user-avatar" id="navAvatar"><?= strtoupper(substr($user['nama_lengkap'], 0, 2)) ?></div>
          <span class="user-name" id="navName"><?= htmlspecialchars($user['nama_lengkap']) ?></span>
          <div class="user-online"></div>
          <span class="user-chevron">▾</span>
        </div>
        <div class="user-dropdown">
          <div class="dropdown-info">
            <div class="dropdown-info-name" id="dropName"><?= htmlspecialchars($user['nama_lengkap']) ?></div>
            <div class="dropdown-info-label" id="dropEmail"><?= htmlspecialchars($user['email']) ?></div>
          </div>
          <a class="dropdown-item active" href="<?= BASE_URL ?>profile">
            <span class="dropdown-icon">👤</span> Profil Saya
          </a>
          <a class="dropdown-item" href="<?= BASE_URL ?>search">
            <span class="dropdown-icon">🔍</span> Cari Produk
          </a>
          <a class="dropdown-item logout" href="<?= BASE_URL ?>logout">
            <span class="dropdown-icon">↩</span> Keluar
          </a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Main Page Content -->
  <div class="page-content" id="pageContent">

    <!-- Profile Hero -->
    <div class="profile-hero">
      <div class="hero-avatar-wrap">
        <div class="hero-avatar" id="heroAvatar"><?= strtoupper(substr($user['nama_lengkap'], 0, 2)) ?></div>
        <div class="hero-badge"></div>
      </div>
      <div class="hero-info">
        <div class="hero-name" id="heroName"><?= htmlspecialchars($user['nama_lengkap']) ?></div>
        <div class="hero-email" id="heroEmail"><?= htmlspecialchars($user['email']) ?></div>
        <div class="hero-meta">
          <div class="hero-meta-sep"></div>
          <div class="hero-meta-sep"></div>
        </div>
      </div>
      <div class="hero-actions">
        <button class="btn-edit" onclick="openEdit()">Edit Profil</button>
        <a class="btn-ghost" href="<?= BASE_URL ?>logout">Keluar ↩</a>
      </div>
    </div>

    <!-- Grid Content -->
    <div class="profile-grid">

      <!-- Informasi Akun -->
      <div class="panel wide">
        <div class="panel-header">
          <div class="panel-title">Informasi Akun</div>
          <span class="panel-action" onclick="openEdit()">Edit →</span>
        </div>
        <div class="info-row">
          <div class="info-label">Nama Lengkap</div>
          <div class="info-value" id="infoName"><?= htmlspecialchars($user['nama_lengkap']) ?></div>
        </div>
        <div class="info-divider"></div>
        <div class="info-row">
          <div class="info-label">Alamat Email</div>
          <div class="info-value" id="infoEmail"><?= htmlspecialchars($user['email']) ?></div>
        </div>
      </div>

      <!-- Edit Profile Modal -->
      <div class="modal-overlay" id="editModal" onclick="if(event.target===this) closeEdit()">
        <div class="modal-box">
          <div class="modal-title">Edit Profil</div>
          <div class="form-group">
            <div class="form-label">Nama Lengkap</div>
            <input class="form-input" id="editName" type="text" placeholder="Masukkan nama kamu" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" />
          </div>
          <div class="form-group">
            <div class="form-label">Alamat Email</div>
            <input class="form-input" id="editEmail" type="email" placeholder="email@contoh.com" value="<?= htmlspecialchars($user['email']) ?>" />
          </div>
          <div class="form-group">
            <div class="form-label">Password Baru (opsional)</div>
            <input class="form-input" id="editPass" type="password" placeholder="Biarkan kosong jika tidak ganti" />
          </div>
          <div class="modal-actions">
            <button class="btn-save" onclick="saveEdit()">Simpan Perubahan</button>
            <button class="btn-cancel" onclick="closeEdit()">Batal</button>
          </div>
        </div>
      </div>

      <!-- Toast -->
      <div class="toast" id="toast"></div>

      <!-- Inject data PHP session ke JS -->
      <script>
        const SESSION_USER = {
          nama: "<?= htmlspecialchars($user['nama_lengkap'], ENT_QUOTES) ?>",
          email: "<?= htmlspecialchars($user['email'], ENT_QUOTES) ?>"
        };
        const BASE_URL = "<?= BASE_URL ?>";
      </script>
      <script src="./public/js/profile.js"></script>
</body>

</html>