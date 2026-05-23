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
  <link rel="icon" href="<?= $base ?>public/images/logo-b.png" type="image/png">
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
      width: 100%; padding: 12px 15px; margin-top: 8px; margin-bottom: 20px; 
      border-radius: 12px; border: 1px solid rgba(255,255,255,0.2); 
      background: rgba(0,0,0,0.2); color: white;
      font-family: 'Lora', serif;
    }
    .form-control::placeholder { color: rgba(255,255,255,0.5); }
    .form-control:focus, .form-select:focus {
      outline: none; border-color: var(--accent-cyan);
      box-shadow: 0 0 0 2px rgba(46,202,208,0.2);
    }
    .form-select option { background: var(--bg-deep); color: white; }
    label { color: var(--text-light); font-size: 0.9rem; font-weight: 500; letter-spacing: 0.02em; }
    .btn-primary { 
      background: linear-gradient(135deg, var(--accent-cyan), var(--primary)); 
      color: white; padding: 12px 24px; border: none; border-radius: 999px; 
      cursor: pointer; width: 100%; font-family: 'Lora', serif; font-size: 1rem;
      font-weight: 600; letter-spacing: 0.03em; transition: all 0.3s;
    }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(46,202,208,0.3); }
    .text-danger { color: #ff6b6b; font-size: 0.85em; margin-top: -10px; margin-bottom: 15px; display: block; }
    .d-none { display: none !important; }
    h2 { text-align: center; margin-bottom: 30px; color: white; font-family: 'DM Serif Display', serif; font-size: 2rem; letter-spacing: -0.01em;}
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
    <span class="nav-brand" onclick="window.location.href='<?= $base ?>landing'" style="font-family: 'DM Serif Display', serif; font-size: 1.1rem; font-weight: normal; color: var(--text-light); opacity: 0.7; cursor: pointer; text-decoration: none; pointer-events: all;">
      banding<em style="font-family:'DM Serif Display',serif;font-style:italic">.in</em>
    </span>
    <div class="nav-links" id="navLinks">
        <button class="nav-btn" onclick="window.location.href='<?= $base ?>list'"><?= __('search') ?></button>
        <?php 
          $currentLang = $_SESSION['lang'] ?? 'en';
          $nextLang = $currentLang === 'en' ? 'id' : 'en';
          $flag = $currentLang === 'en' ? '🇺🇸' : '🇮🇩';
        ?>
        <button class="nav-btn" style="border-radius: 50%; padding: 5px 10px; font-size: 1.2rem;" onclick="window.location.href='<?= BASE_URL ?>lang/switch?lang=<?= $nextLang ?>'" title="Switch Language">
          <?= $flag ?>
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
                  <label for="platform_id"><?= __('ecommerce_choice') ?></label>
                  <select class="form-select" id="platform_id" name="platform_id" required>
                      <option value="" disabled selected><?= __('choose_ecommerce') ?></option>
                      <option value="1" data-domain="tokopedia">Tokopedia</option>
                      <option value="2" data-domain="lazada">Lazada</option>
                      <option value="3" data-domain="blibli">Blibli</option>
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
