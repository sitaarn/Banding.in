<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$base = (defined('BASE_URL')) ? BASE_URL : 'http://localhost/bandingin/';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'en' ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= __('add_new_product') ?> — Banding.in</title>
  <link rel="icon" href="<?= $base ?>public/images/favicon.png" type="image/png">
  <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?= $base ?>public/css/stylelanding.css">
  <style>
    .main-card { 
      background: rgba(255,255,255,0.05); 
      border-radius: 24px; 
      padding: 40px; 
      margin-top: 120px; 
      box-shadow: 0 8px 40px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.08); 
      border: 1.5px solid rgba(255,255,255,0.1); 
      backdrop-filter: blur(16px);
      position: relative;
      z-index: 10;
    }
    .main-card::after {
      display: none !important;
      content: '' !important;
    }
    .form-control, .form-select { 
      width: 100%; padding: 14px 18px; margin-top: 8px; margin-bottom: 22px; 
      border-radius: 14px; border: 1.5px solid rgba(255,255,255,0.12); 
      background: rgba(255,255,255,0.06); color: white;
      font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      backdrop-filter: blur(8px);
    }
    .form-control::placeholder { color: rgba(255,255,255,0.4); font-style: italic; }
    .form-control:focus, .form-select:focus {
      outline: none; 
      border-color: var(--accent-cyan);
      background: rgba(255,255,255,0.1);
      box-shadow: 0 0 0 4px rgba(46,202,208,0.15);
    }
    .form-select option { background: #1a2235; color: white; }
    label { color: rgba(255,255,255,0.85); font-size: 0.9rem; font-weight: 500; letter-spacing: 0.02em; font-family: system-ui, -apple-system, sans-serif; }
    .btn-primary { 
      background: linear-gradient(135deg, #2ecad0, #2d5a9e); 
      color: white; padding: 14px 24px; border: none; border-radius: 999px; 
      cursor: pointer; width: 100%; font-family: system-ui, -apple-system, sans-serif; font-size: 1.05rem;
      font-weight: 600; letter-spacing: 0.03em; transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(46,202,208,0.2);
    }
    .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(46,202,208,0.4); }
    .text-danger { color: #ff6b6b; font-size: 0.85em; margin-top: -10px; margin-bottom: 15px; display: block; }
    .d-none { display: none !important; }
    h2 { text-align: center; margin-bottom: 30px; color: white; font-family: 'DM Serif Display', serif; font-size: 2rem; letter-spacing: -0.01em;}
    
    .alert { padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-size: 0.95rem; font-family: system-ui, -apple-system, sans-serif; font-weight: 500; text-align: center; }
    .alert-success { background: rgba(46,202,208,0.15); color: #2ecad0; border: 1px solid rgba(46,202,208,0.3); }
    .alert-error { background: rgba(255,107,107,0.15); color: #ff6b6b; border: 1px solid rgba(255,107,107,0.3); }
    
    body { overflow-y: auto !important; }
    .main-card { margin-bottom: 60px; }
  </style>
</head>
<body>
  <!-- BACKGROUND ANIMATIONS -->
  <div class="bg"></div>
  <div class="blobs">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
  </div>
  <div class="grid-overlay"></div>

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

  <main style="max-width: 600px; margin: 0 auto; padding: 0 20px;">
      <div class="main-card">
          <h2><i class="fa-solid fa-plus-circle" style="color: var(--accent-cyan);"></i> <?= __('add_new_product') ?></h2>
          <?php displayFlashMessage(); ?>

          <form action="<?= BASE_URL . 'seller/store' ?>" method="post" id="addProductForm">
              <div>
                  <label for="name"><?= __('product_name') ?></label>
                  <input type="text" class="form-control" id="name" name="name" required placeholder="<?= __('product_name_ph') ?>">
              </div>

              <div>
                  <label for="category">Kategori Produk</label>
                  <select class="form-select" id="category" name="category" required>
                      <option value="" disabled selected>Pilih Kategori</option>
                      <option value="Elektronik">Elektronik</option>
                      <option value="Pakaian">Pakaian</option>
                      <option value="Kecantikan">Kecantikan</option>
                      <option value="Peralatan Rumah">Peralatan Rumah</option>
                      <option value="Otomotif">Otomotif</option>
                      <option value="Lainnya">Lainnya</option>
                  </select>
              </div>

              <div>
                  <label for="platform_id"><?= __('ecommerce_choice') ?></label>
                  <select class="form-select" id="platform_id" name="platform_id" required>
                      <option value="" disabled selected><?= __('choose_ecommerce') ?></option>
                      <option value="2" data-domain="tokopedia">Tokopedia</option>
                      <option value="3" data-domain="lazada">Lazada</option>
                      <option value="4" data-domain="blibli">Blibli</option>
                  </select>
              </div>

              <div>
                  <label for="link"><?= __('product_link') ?></label>
                  <input type="url" class="form-control" id="link" name="link" required placeholder="https://...">
                  <div id="linkHelp" class="text-danger d-none"><?= __('product_link_err') ?></div>
              </div>

              <div>
                  <label for="price"><?= __('product_price') ?></label>
                  <input type="number" class="form-control" id="price" name="price" required min="0" placeholder="<?= __('product_price_ph') ?>">
              </div>

              <div style="margin-top: 10px;">
                  <button type="submit" class="btn-primary" id="btnSubmit"><?= __('save_product') ?></button>
              </div>
          </form>
      </div>
  </main>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
      const form = document.getElementById('addProductForm');
      const platformSelect = document.getElementById('platform_id');
      const linkInput = document.getElementById('link');
      const linkHelp = document.getElementById('linkHelp');

      function validateLink() {
          if (!platformSelect.value || !linkInput.value) return true;
          
          const selectedOption = platformSelect.options[platformSelect.selectedIndex];
          const domain = selectedOption.getAttribute('data-domain');
          const linkValue = linkInput.value.toLowerCase();

          if (linkValue.includes(domain)) {
              linkInput.style.borderColor = '#ccc';
              linkHelp.classList.add('d-none');
              return true;
          } else {
              linkInput.style.borderColor = 'red';
              linkHelp.classList.remove('d-none');
              return false;
          }
      }

      platformSelect.addEventListener('change', validateLink);
      linkInput.addEventListener('input', validateLink);

      form.addEventListener('submit', function(e) {
          if (!validateLink()) {
              e.preventDefault();
              alert('<?= __('link_alert') ?>');
          }
      });
  });
  </script>
</body>
</html>
