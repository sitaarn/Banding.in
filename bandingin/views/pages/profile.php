<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Banding.in — <?= __('my_profile') ?></title>
  <link rel="icon" href="<?= BASE_URL ?>public/images/favicon.png" type="image/png">
  <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/profile.css?v=<?= time() ?>">
</head>
<body>

<!-- Background layers -->
<div class="bg"></div>
<div class="blobs"><div class="blob blob-1"></div><div class="blob blob-2"></div><div class="blob blob-3"></div></div>
<div class="orbs">
  <div class="orb orb-1"></div><div class="orb orb-2"></div><div class="orb orb-3"></div>
  <div class="orb orb-4"></div><div class="orb orb-5"></div><div class="orb orb-6"></div>
  <div class="orb orb-7"></div>
</div>
<div class="grid-overlay"></div>

<!-- Navigation -->
<nav id="mainNav">
  <a class="nav-brand" href="<?= BASE_URL ?>list">banding<em style="font-family:'DM Serif Display',serif;font-style:italic">.in</em></a>
  <div class="nav-links" id="navLinks">
    <a class="nav-btn" href="<?= BASE_URL ?>list"><?= __('search_products') ?></a>
    
    <?php if(isSeller()) : ?>
    <button class="nav-btn"
            style="background: linear-gradient(135deg, #2ecad0, #2d5a9e) !important; color: white !important; border: none !important;"
            onclick="window.location.href='<?= BASE_URL ?>seller/add'">
      <i class="fa-solid fa-plus-circle"></i> <?= __('add_product') ?>
    </button>
    <?php elseif(isset($_SESSION['role']) && $_SESSION['role'] === 'user') : ?>
    <button class="nav-btn favorite-nav-btn"
            onclick="window.location.href='<?= BASE_URL ?>favorit'">
      <i class="fa-solid fa-heart"></i> <?= __('favorite') ?>
    </button>
    <?php endif; ?>

    <div class="user-chip-wrap" id="userChipWrap">
      <div class="user-chip" onclick="toggleDropdown()">
        <div class="user-avatar" id="navAvatar"><?= get_initials($user['nama_lengkap'] ?? '') ?></div>
        <span class="user-name" id="navName"><?= htmlspecialchars($user['nama_lengkap'] ?? '') ?></span>
        <div class="user-online"></div>
        <span class="user-chevron">▾</span>
      </div>
      <div class="user-dropdown">
        <div class="dropdown-info">
          <div class="dropdown-info-name" id="dropName"><?= htmlspecialchars($user['username'] ?? '') ?></div>
          <div class="dropdown-info-label"><?= __('logged_in_status') ?></div>
        </div>
        <a class="dropdown-item active" href="<?= BASE_URL ?>profile"><span class="dropdown-icon"><i class="fa-solid fa-circle-user"></i></span> <?= __('my_profile') ?></a>
        <?php if(isSeller()): ?>
        <a class="dropdown-item" href="<?= BASE_URL ?>seller/products">
          <span class="dropdown-icon"><i class="fa-solid fa-box"></i></span> <?= __('manage_products') ?>
        </a>
        <?php endif; ?>
        <?php if(isSuperAdmin()): ?>
        <a class="dropdown-item" href="<?= BASE_URL ?>admin/dashboard">
          <span class="dropdown-icon"><i class="fa-solid fa-shield-halved"></i></span> <?= __('admin_panel') ?>
        </a>
        <?php endif; ?>
        <a class="dropdown-item logout" href="<?= BASE_URL ?>logout"><span class="dropdown-icon"><i class="fa-solid fa-right-from-bracket"></i></span> <?= __('logout') ?></a>
      </div>
    </div>

    <a class="nav-btn" href="<?= BASE_URL ?>aboutus"><?= __('about_us') ?></a>

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

<div class="page-content" id="pageContent">
  <!-- Profile Hero -->
  <div class="profile-hero">
    <div class="hero-avatar-wrap">
      <div class="hero-avatar" id="heroAvatar"><?= get_initials($user['nama_lengkap'] ?? '') ?></div>
      <div class="hero-badge"></div>
    </div>
    <div class="hero-info">
      <div class="hero-name" id="heroName"><?= htmlspecialchars($user['nama_lengkap'] ?? '') ?></div>
      <div class="hero-email" id="heroEmail"><?= htmlspecialchars($user['email'] ?? '') ?></div>
    </div>
    <div class="hero-actions">
      <button class="btn-edit" onclick="openEdit()"><?= __('edit_profile') ?></button>
      <a class="btn-ghost" href="<?= BASE_URL ?>logout"><?= __('logout') ?> ↩</a>
    </div>
  </div>

  <!-- Informasi Akun -->
  <div class="profile-hero" style="flex-direction: column; align-items: flex-start;">
    <div class="panel-header" style="width: 100%; display: flex; justify-content: space-between; align-items: center;">
      <div class="panel-title"><?= __('account_info') ?></div>
      <span class="panel-action" onclick="openEdit()">Edit →</span>
    </div>
    <div class="info-row">
      <div class="info-label"><?= __('full_name') ?></div>
      <div class="info-value" id="infoName"><?= htmlspecialchars($user['nama_lengkap'] ?? '') ?></div>
    </div>
    <div class="info-divider" style="width:100%"></div>
    <div class="info-row">
      <div class="info-label"><?= __('email_address') ?></div>
      <div class="info-value" id="infoEmail"><?= htmlspecialchars($user['email'] ?? '') ?></div>
    </div>
  </div>

  <!-- Modal Edit -->
  <div class="modal-overlay" id="editModal" onclick="if(event.target===this) closeEdit()">
    <div class="modal-box">
      <div class="modal-title"><?= __('edit_profile') ?></div>
      <div class="form-group">
        <div class="form-label"><?= __('full_name') ?></div>
        <input class="form-input" id="editName" type="text" value="<?= htmlspecialchars($user['nama_lengkap'] ?? '') ?>" />
      </div>
      <div class="form-group">
        <div class="form-label"><?= __('email_address') ?></div>
        <input class="form-input" id="editEmail" type="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" />
      </div>
      <div class="form-group">
        <div class="form-label"><?= __('new_password') ?></div>
        <input class="form-input" id="editPass" type="password" placeholder="<?= __('new_password_ph') ?>" />
      </div>
      <div class="modal-actions">
        <button class="btn-save" onclick="saveEdit()"><?= __('save_changes') ?></button>
        <button class="btn-cancel" onclick="closeEdit()"><?= __('cancel') ?></button>
      </div>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
  const SESSION_USER = {
    nama: "<?= htmlspecialchars($user['nama_lengkap'] ?? '', ENT_QUOTES) ?>",
    email: "<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES) ?>"
  };
  const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= BASE_URL ?>public/js/profile.js"></script>
</body>
</html>