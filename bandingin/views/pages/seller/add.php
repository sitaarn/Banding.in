<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$base = (defined('BASE_URL')) ? BASE_URL : 'http://localhost/bandingin/';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'id' ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= __('add_new_product') ?> — Banding.in</title>
  <link rel="icon" href="<?= $base ?>public/images/favicon.png" type="image/png">
  <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?= $base ?>public/css/stylelanding.css">
  <style>
    html, body { overflow-y: auto !important; }

    .add-container {
      position: relative;
      z-index: 10;
      max-width: 640px;
      margin: 0 auto;
      padding: 100px 24px 60px;
    }

    .add-card {
      background: var(--bg-mid);
      border-radius: 24px;
      padding: 40px;
      border: 1px solid var(--glass-border);
      box-shadow: 0 8px 40px rgba(0,0,0,0.06);
      animation: fadeUp 0.6s ease both;
    }

    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .add-card-header {
      text-align: center;
      margin-bottom: 32px;
    }

    .add-card-header .header-icon {
      width: 52px;
      height: 52px;
      border-radius: 16px;
      background: linear-gradient(135deg, #2ecad0, #2d5a9e);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
      color: white;
      margin-bottom: 16px;
      box-shadow: 0 4px 15px rgba(46,202,208,0.3);
    }

    .add-card-header h2 {
      font-family: 'DM Serif Display', serif;
      font-size: 1.6rem;
      color: var(--primary);
      margin-bottom: 6px;
    }

    .add-card-header p {
      color: var(--text-soft);
      font-size: 0.85rem;
    }

    /* Form fields */
    .form-group {
      margin-bottom: 22px;
    }

    .form-label {
      display: block;
      font-size: 0.82rem;
      font-weight: 600;
      color: var(--primary);
      margin-bottom: 8px;
      letter-spacing: 0.02em;
    }

    .form-label .required {
      color: #e05252;
      margin-left: 2px;
    }

    .form-input,
    .form-select {
      width: 100%;
      padding: 14px 18px;
      border-radius: 14px;
      border: 1.5px solid var(--glass-border);
      background: var(--bg-surface);
      color: var(--primary);
      font-family: inherit;
      font-size: 0.9rem;
      transition: all 0.3s ease;
    }

    .form-input::placeholder {
      color: var(--text-soft);
      font-style: italic;
    }

    .form-input:focus,
    .form-select:focus {
      outline: none;
      border-color: #2ecad0;
      background: var(--bg-mid);
      box-shadow: 0 0 0 4px rgba(46,202,208,0.1);
    }

    .form-select {
      cursor: pointer;
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath d='M2 4l4 4 4-4' fill='none' stroke='%235f5f5d' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 16px center;
      padding-right: 40px;
    }

    .form-select option {
      background: var(--bg-mid);
      color: var(--primary);
      padding: 10px;
    }

    .form-input-icon {
      position: relative;
    }

    .form-input-icon .icon {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-soft);
      font-size: 0.85rem;
    }

    .form-input-icon .form-input {
      padding-left: 44px;
    }

    .form-input-prefix {
      position: relative;
    }

    .form-input-prefix .prefix {
      position: absolute;
      left: 18px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 0.88rem;
      font-weight: 600;
      color: var(--text-soft);
    }

    .form-input-prefix .form-input {
      padding-left: 44px;
    }

    /* Validation */
    .form-error {
      color: #e05252;
      font-size: 0.78rem;
      margin-top: 6px;
      display: none;
    }

    .form-error.visible {
      display: block;
    }

    /* Alert messages */
    .alert {
      padding: 14px 18px;
      border-radius: 14px;
      margin-bottom: 20px;
      font-size: 0.88rem;
      font-weight: 500;
      text-align: center;
      animation: fadeUp 0.4s ease both;
    }

    .alert-success {
      background: rgba(77,214,122,0.1);
      color: #2da84f;
      border: 1px solid rgba(77,214,122,0.25);
    }

    .alert-danger, .alert-error {
      background: rgba(224,82,82,0.1);
      color: #e05252;
      border: 1px solid rgba(224,82,82,0.25);
    }

    /* Submit button */
    .btn-submit {
      width: 100%;
      padding: 16px 24px;
      border-radius: 999px;
      border: none;
      background: linear-gradient(135deg, #2ecad0, #2d5a9e);
      color: white;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      box-shadow: 0 4px 15px rgba(46,202,208,0.25);
      margin-top: 8px;
      font-family: inherit;
      letter-spacing: 0.03em;
    }

    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(46,202,208,0.4);
    }

    .btn-submit:active {
      transform: translateY(0);
    }

    /* Divider */
    .form-divider {
      height: 1px;
      background: var(--glass-border);
      margin: 28px 0;
    }

    /* Back link */
    .back-link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      color: var(--text-soft);
      font-size: 0.82rem;
      text-decoration: none;
      margin-top: 20px;
      transition: color 0.2s;
    }

    .back-link:hover {
      color: #2ecad0;
    }

    @media (max-width: 640px) {
      .add-card {
        padding: 28px 22px;
      }
    }
  </style>
</head>
<body>

  <!-- BACKGROUND -->
  <div class="bg"></div>
  <div class="blobs">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
  </div>
  <div class="grid-overlay"></div>

  <!-- NAV -->
  <nav>
    <span class="nav-brand" onclick="window.location.href='<?= $base ?>landing'" style="font-family:'DM Serif Display',serif; cursor:pointer; pointer-events:all;">
      banding<em style="font-family:'DM Serif Display',serif;font-style:italic">.in</em>
    </span>
    <div class="nav-links" id="navLinks">
      <button class="nav-btn" onclick="window.location.href='<?= $base ?>list'"><?= __('search') ?></button>
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

  <!-- MAIN -->
  <div class="add-container">
    <div class="add-card">

      <div class="add-card-header">
        <div class="header-icon"><i class="fa-solid fa-plus"></i></div>
        <h2><?= __('add_new_product') ?></h2>
        <p><?= __('add_product_desc') ?></p>
      </div>

      <?php displayFlashMessage(); ?>

      <form action="<?= BASE_URL . 'seller/store' ?>" method="post" id="addProductForm">

        <!-- Nama Produk -->
        <div class="form-group">
          <label class="form-label"><?= __('product_name') ?> <span class="required">*</span></label>
          <div class="form-input-icon">
            <i class="fa-solid fa-tag icon"></i>
            <input type="text" class="form-input" id="name" name="name" required placeholder="<?= __('product_name_ph') ?>">
          </div>
        </div>

        <!-- Kategori -->
        <div class="form-group">
          <label class="form-label"><?= __('product_category') ?> <span class="required">*</span></label>
          <select class="form-select" id="category" name="category" required>
            <option value="" disabled selected><?= __('choose_category') ?></option>
            <option value="Elektronik"><?= __('cat_electronics') ?></option>
            <option value="Pakaian"><?= __('cat_clothing') ?></option>
            <option value="Kecantikan"><?= __('cat_beauty') ?></option>
            <option value="Peralatan Rumah"><?= __('cat_household') ?></option>
            <option value="Otomotif"><?= __('cat_automotive') ?></option>
            <option value="Lainnya"><?= __('cat_others') ?></option>
          </select>
        </div>

        <!-- Platform -->
        <div class="form-group">
          <label class="form-label"><?= __('ecommerce_choice') ?> <span class="required">*</span></label>
          <select class="form-select" id="platform_id" name="platform_id" required>
            <option value="" disabled selected><?= __('choose_ecommerce') ?></option>
            <option value="2" data-domain="tokopedia">Tokopedia</option>
            <option value="3" data-domain="lazada">Lazada</option>
            <option value="4" data-domain="blibli">Blibli</option>
          </select>
        </div>

        <div class="form-divider"></div>

        <!-- Link Produk -->
        <div class="form-group">
          <label class="form-label"><?= __('product_link') ?> <span class="required">*</span></label>
          <div class="form-input-icon">
            <i class="fa-solid fa-link icon"></i>
            <input type="url" class="form-input" id="link" name="link" required placeholder="https://www.tokopedia.com/...">
          </div>
          <div class="form-error" id="linkHelp"><?= __('product_link_err') ?></div>
        </div>

        <!-- Harga -->
        <div class="form-group">
          <label class="form-label"><?= __('product_price') ?> <span class="required">*</span></label>
          <div class="form-input-prefix">
            <span class="prefix">Rp</span>
            <input type="text" class="form-input" id="priceDisplay" required placeholder="<?= __('product_price_ph') ?>" inputmode="numeric" autocomplete="off">
            <input type="hidden" id="price" name="price">
          </div>
        </div>

        <button type="submit" class="btn-submit" id="btnSubmit">
          <i class="fa-solid fa-check"></i> <?= __('save_product') ?>
        </button>
      </form>


    </div>
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addProductForm');
    const platformSelect = document.getElementById('platform_id');
    const linkInput = document.getElementById('link');
    const linkHelp = document.getElementById('linkHelp');
    const priceDisplay = document.getElementById('priceDisplay');
    const priceHidden = document.getElementById('price');

    // Currency formatting
    function formatCurrency(value) {
      return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    priceDisplay.addEventListener('input', function() {
      let raw = this.value.replace(/\D/g, '');
      if (raw.length > 15) raw = raw.slice(0, 15);
      priceHidden.value = raw;
      this.value = raw ? formatCurrency(raw) : '';
    });

    priceDisplay.addEventListener('keydown', function(e) {
      // Allow: backspace, delete, tab, escape, enter, arrows
      if ([8, 9, 27, 13, 46, 37, 38, 39, 40].includes(e.keyCode)) return;
      // Allow Ctrl+A/C/V/X
      if ((e.ctrlKey || e.metaKey) && [65, 67, 86, 88].includes(e.keyCode)) return;
      // Block non-numeric
      if ((e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
      }
    });

    function validateLink() {
      if (!platformSelect.value || !linkInput.value) return true;
      
      const selectedOption = platformSelect.options[platformSelect.selectedIndex];
      const domain = selectedOption.getAttribute('data-domain');
      const linkValue = linkInput.value.toLowerCase();

      if (linkValue.includes(domain)) {
        linkInput.style.borderColor = '';
        linkHelp.classList.remove('visible');
        return true;
      } else {
        linkInput.style.borderColor = '#e05252';
        linkHelp.classList.add('visible');
        return false;
      }
    }

    platformSelect.addEventListener('change', validateLink);
    linkInput.addEventListener('input', validateLink);

    form.addEventListener('submit', function(e) {
      if (!validateLink()) {
        e.preventDefault();
        alert('<?= __('link_alert') ?>');
        return;
      }
      if (!priceHidden.value || parseInt(priceHidden.value) <= 0) {
        e.preventDefault();
        priceDisplay.focus();
        priceDisplay.style.borderColor = '#e05252';
        setTimeout(() => priceDisplay.style.borderColor = '', 1800);
        return;
      }
    });
  });
  </script>
</body>
</html>
