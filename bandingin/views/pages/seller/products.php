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
  <title><?= __('manage_products') ?> — Banding.in</title>
  <link rel="icon" href="<?= $base ?>public/images/favicon.png" type="image/png">
  <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?= $base ?>public/css/stylelanding.css">
  <style>
    html, body { overflow-y: auto !important; }

    .seller-container {
      position: relative;
      z-index: 10;
      max-width: 960px;
      margin: 0 auto;
      padding: 100px 24px 60px;
    }

    .seller-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 28px;
      flex-wrap: wrap;
      gap: 16px;
    }

    .seller-header-left h1 {
      font-family: 'DM Serif Display', serif;
      font-size: 1.8rem;
      color: var(--primary);
      margin-bottom: 4px;
    }

    .seller-header-left p {
      color: var(--text-soft);
      font-size: 0.88rem;
    }

    .seller-stats {
      display: flex;
      gap: 12px;
    }

    .seller-stat-chip {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 10px 18px;
      border-radius: 14px;
      background: var(--bg-mid);
      border: 1px solid var(--glass-border);
      box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }

    .seller-stat-chip .stat-num {
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--primary);
    }

    .seller-stat-chip .stat-lbl {
      font-size: 0.72rem;
      color: var(--text-soft);
      line-height: 1.2;
    }

    .seller-btn-add {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 12px 24px;
      border-radius: 999px;
      border: none;
      background: linear-gradient(135deg, #2ecad0, #2d5a9e);
      color: white;
      font-size: 0.88rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
      box-shadow: 0 4px 15px rgba(46,202,208,0.25);
      text-decoration: none;
    }

    .seller-btn-add:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(46,202,208,0.4);
    }

    /* Search bar */
    .seller-search-wrap {
      position: relative;
      margin-bottom: 20px;
    }

    .seller-search-icon {
      position: absolute;
      left: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-soft);
      font-size: 0.9rem;
    }

    .seller-search {
      width: 100%;
      padding: 14px 18px 14px 46px;
      border-radius: 14px;
      border: 1px solid var(--glass-border);
      background: var(--bg-mid);
      font-size: 0.9rem;
      color: var(--primary);
      transition: all 0.3s;
      font-family: inherit;
    }

    .seller-search::placeholder { color: var(--text-soft); }
    .seller-search:focus {
      outline: none;
      border-color: #2ecad0;
      box-shadow: 0 0 0 4px rgba(46,202,208,0.1);
    }

    /* Product Cards */
    .product-grid {
      display: flex;
      flex-direction: column;
      gap: 14px;
    }

    .product-card {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 18px 22px;
      border-radius: 16px;
      background: var(--bg-mid);
      border: 1px solid var(--glass-border);
      box-shadow: 0 2px 12px rgba(0,0,0,0.04);
      transition: all 0.3s;
      animation: cardIn 0.4s ease both;
    }

    .product-card:hover {
      box-shadow: 0 6px 24px rgba(0,0,0,0.08);
      transform: translateY(-2px);
    }

    @keyframes cardIn {
      from { opacity: 0; transform: translateY(12px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .product-num {
      width: 32px;
      height: 32px;
      border-radius: 10px;
      background: var(--bg-surface);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.78rem;
      font-weight: 700;
      color: var(--text-mid);
      flex-shrink: 0;
    }

    .product-info {
      flex: 1;
      min-width: 0;
    }

    .product-name {
      font-size: 0.92rem;
      font-weight: 600;
      color: var(--primary);
      line-height: 1.3;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .product-meta {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-top: 4px;
      font-size: 0.76rem;
      color: var(--text-soft);
      flex-wrap: wrap;
    }

    .product-platform-badge {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 2px 10px;
      border-radius: 999px;
      font-size: 0.7rem;
      font-weight: 600;
      color: white;
    }

    .product-status {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 3px 10px;
      border-radius: 999px;
      font-size: 0.68rem;
      font-weight: 600;
      letter-spacing: 0.03em;
    }

    .status-approved { background: rgba(77,214,122,0.12); color: #2da84f; }
    .status-pending { background: rgba(232,125,62,0.12); color: #e87d3e; }
    .status-rejected { background: rgba(224,82,82,0.12); color: #e05252; }
    .status-taken_down { background: rgba(224,82,82,0.12); color: #e05252; }

    .product-price-wrap {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 4px;
      flex-shrink: 0;
    }

    .product-price {
      font-size: 1rem;
      font-weight: 700;
      color: var(--primary);
      white-space: nowrap;
    }

    .product-actions {
      display: flex;
      gap: 8px;
      flex-shrink: 0;
    }

    .product-btn {
      width: 36px;
      height: 36px;
      border-radius: 10px;
      border: 1px solid var(--glass-border);
      background: var(--bg-surface);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.2s;
      font-size: 0.82rem;
      color: var(--text-mid);
      text-decoration: none;
    }

    .product-btn:hover {
      background: var(--bg-mid);
      border-color: #2ecad0;
      color: #2ecad0;
      box-shadow: 0 2px 8px rgba(46,202,208,0.15);
    }

    .product-btn.danger:hover {
      border-color: #e05252;
      color: #e05252;
      box-shadow: 0 2px 8px rgba(224,82,82,0.15);
    }

    /* Empty state */
    .seller-empty {
      text-align: center;
      padding: 60px 20px;
      background: var(--bg-mid);
      border-radius: 20px;
      border: 1px solid var(--glass-border);
    }

    .seller-empty-icon {
      font-size: 3.5rem;
      margin-bottom: 16px;
    }

    .seller-empty-title {
      font-family: 'DM Serif Display', serif;
      font-size: 1.3rem;
      color: var(--primary);
      margin-bottom: 8px;
    }

    .seller-empty-text {
      color: var(--text-soft);
      font-size: 0.88rem;
      margin-bottom: 24px;
    }

    /* Toast */
    .toast {
      position: fixed;
      bottom: 30px;
      left: 50%;
      transform: translateX(-50%) translateY(80px);
      padding: 14px 28px;
      border-radius: 999px;
      color: white;
      font-size: 0.85rem;
      font-weight: 500;
      background: rgba(42,157,143,0.92);
      backdrop-filter: blur(10px);
      box-shadow: 0 6px 24px rgba(0,0,0,0.15);
      z-index: 999;
      opacity: 0;
      transition: all 0.4s cubic-bezier(0.16,1,0.3,1);
      pointer-events: none;
    }

    .toast.visible {
      opacity: 1;
      transform: translateX(-50%) translateY(0);
    }

    /* Confirm Modal */
    .confirm-overlay {
      position: fixed;
      inset: 0;
      z-index: 500;
      background: rgba(0,0,0,0.35);
      backdrop-filter: blur(4px);
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.3s;
    }

    .confirm-overlay.visible {
      opacity: 1;
      pointer-events: all;
    }

    .confirm-box {
      background: var(--bg-mid);
      border-radius: 20px;
      padding: 32px;
      max-width: 400px;
      width: 90%;
      box-shadow: 0 20px 60px rgba(0,0,0,0.15);
      text-align: center;
    }

    .confirm-icon {
      font-size: 2.5rem;
      margin-bottom: 12px;
    }

    .confirm-title {
      font-family: 'DM Serif Display', serif;
      font-size: 1.2rem;
      color: var(--primary);
      margin-bottom: 8px;
    }

    .confirm-text {
      color: var(--text-soft);
      font-size: 0.85rem;
      margin-bottom: 24px;
      line-height: 1.5;
    }

    .confirm-actions {
      display: flex;
      gap: 10px;
    }

    .confirm-btn {
      flex: 1;
      padding: 12px;
      border-radius: 12px;
      border: none;
      font-size: 0.85rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
    }

    .confirm-btn-yes {
      background: #e05252;
      color: white;
    }

    .confirm-btn-yes:hover {
      background: #c74040;
    }

    .confirm-btn-no {
      background: var(--bg-surface);
      color: var(--text-mid);
      border: 1px solid var(--glass-border);
    }

    .confirm-btn-no:hover {
      background: var(--bg-mid);
    }

    @media (max-width: 640px) {
      .seller-header {
        flex-direction: column;
        align-items: flex-start;
      }

      .product-card {
        flex-wrap: wrap;
      }

      .product-price-wrap {
        width: 100%;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        padding-top: 8px;
        border-top: 1px solid var(--glass-border);
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
      <button class="nav-btn" onclick="window.location.href='<?= $base ?>list'"><?= __('search_products') ?></button>

      <?php 
        $user_nama = $_SESSION['nama_lengkap'] ?? 'User';
        $user_uname = $_SESSION['username'] ?? 'User';
      ?>
      <div class="user-chip-wrap" id="userChipWrap">
        <div class="user-chip" onclick="toggleDropdown()">
          <div class="user-avatar" data-avatar="<?= htmlspecialchars($user_nama) ?>"><?= get_initials($user_nama) ?></div>
          <span class="user-name"><?= htmlspecialchars($user_nama) ?></span>
          <div class="user-online"></div>
          <span class="user-chevron">▼</span>
        </div>
        <div class="user-dropdown">
          <div class="dropdown-info">
            <div class="dropdown-info-name"><?= htmlspecialchars($user_uname) ?></div>
            <div class="dropdown-info-label">Seller ✓</div>
          </div>
          <a class="dropdown-item" href="<?= $base ?>profile">
            <span class="dropdown-icon">👤</span> <?= __('my_profile') ?>
          </a>
          <a class="dropdown-item active" href="<?= $base ?>seller/products">
            <span class="dropdown-icon">📦</span> <?= __('manage_products') ?>
          </a>
          <a class="dropdown-item logout" href="<?= $base ?>logout">
            <span class="dropdown-icon">↩</span> <?= __('logout') ?>
          </a>
        </div>
      </div>

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

  <!-- MAIN CONTENT -->
  <div class="seller-container">

    <!-- Header -->
    <div class="seller-header">
      <div class="seller-header-left">
        <h1><?= __('manage_products') ?></h1>
        <p>Lihat dan kelola semua produk yang kamu submit</p>
      </div>
      <div style="display: flex; align-items: center; gap: 16px;">
        <div class="seller-stats">
          <div class="seller-stat-chip">
            <span class="stat-num"><?= count($products ?? []) ?></span>
            <span class="stat-lbl">Total<br>Produk</span>
          </div>
          <?php
            $approvedCount = 0;
            $pendingCount = 0;
            foreach(($products ?? []) as $p) {
              if(($p['status'] ?? '') === 'approved') $approvedCount++;
              if(($p['status'] ?? '') === 'pending') $pendingCount++;
            }
          ?>
          <div class="seller-stat-chip">
            <span class="stat-num" style="color: #2da84f;"><?= $approvedCount ?></span>
            <span class="stat-lbl">Approved</span>
          </div>
          <div class="seller-stat-chip">
            <span class="stat-num" style="color: #e87d3e;"><?= $pendingCount ?></span>
            <span class="stat-lbl">Pending</span>
          </div>
        </div>
        <a class="seller-btn-add" href="<?= $base ?>seller/add">
          <i class="fa-solid fa-plus"></i> <?= __('add_product') ?>
        </a>
      </div>
    </div>

    <!-- Search -->
    <div class="seller-search-wrap">
      <i class="fa-solid fa-magnifying-glass seller-search-icon"></i>
      <input class="seller-search" type="text" id="searchInput" placeholder="Cari nama produk..." oninput="filterProducts()">
    </div>

    <!-- Product List -->
    <div class="product-grid" id="productGrid">
      <?php if(!empty($products)): ?>
        <?php foreach($products as $i => $p): ?>
          <?php
            $pfColors = [
              'Tokopedia' => '#42b549',
              'Lazada' => '#0f146b',
              'Blibli' => '#0095d9'
            ];
            $pfName = $p['platform_name'] ?? 'Unknown';
            $pfColor = $pfColors[$pfName] ?? '#888';
            $status = $p['status'] ?? 'approved';
          ?>
          <div class="product-card" data-name="<?= strtolower(htmlspecialchars($p['name'] ?? '')) ?>" style="animation-delay: <?= $i * 0.05 ?>s">
            <div class="product-num"><?= $i + 1 ?></div>
            <div class="product-info">
              <div class="product-name"><?= htmlspecialchars($p['name'] ?? '') ?></div>
              <div class="product-meta">
                <span class="product-platform-badge" style="background: <?= $pfColor ?>"><?= htmlspecialchars($pfName) ?></span>
                <span><?= htmlspecialchars($p['category'] ?? 'Lainnya') ?></span>
                <span class="product-status status-<?= $status ?>">
                  <?php if($status === 'approved'): ?>✓ Approved
                  <?php elseif($status === 'pending'): ?>⏳ Pending
                  <?php elseif($status === 'rejected'): ?>✕ Ditolak
                  <?php elseif($status === 'taken_down'): ?>⛔ Diturunkan
                  <?php else: ?><?= ucfirst($status) ?>
                  <?php endif; ?>
                </span>
              </div>
            </div>
            <div class="product-price-wrap">
              <span class="product-price">Rp <?= number_format($p['price'] ?? 0, 0, ',', '.') ?></span>
            </div>
            <div class="product-actions">
              <?php if(!empty($p['link'])): ?>
              <a class="product-btn" href="<?= htmlspecialchars($p['link']) ?>" target="_blank" title="Lihat di marketplace">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
              </a>
              <?php endif; ?>
              <button class="product-btn danger" onclick="confirmDeleteProduct(<?= $p['id'] ?>, this)" title="Hapus produk">
                <i class="fa-solid fa-trash-can"></i>
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="seller-empty">
          <div class="seller-empty-icon">📭</div>
          <div class="seller-empty-title">Belum ada produk</div>
          <div class="seller-empty-text">Mulai tambahkan produk pertamamu ke marketplace!</div>
          <a class="seller-btn-add" href="<?= $base ?>seller/add">
            <i class="fa-solid fa-plus"></i> Tambah Produk Pertama
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Toast -->
  <div class="toast" id="toast"></div>

  <!-- Confirm Modal -->
  <div class="confirm-overlay" id="confirmModal" onclick="if(event.target===this) closeConfirm()">
    <div class="confirm-box">
      <div class="confirm-icon">🗑️</div>
      <div class="confirm-title">Hapus Produk?</div>
      <div class="confirm-text">Produk ini akan dihapus permanen dan tidak bisa dikembalikan.</div>
      <div class="confirm-actions">
        <button class="confirm-btn confirm-btn-yes" id="confirmYes">Ya, Hapus</button>
        <button class="confirm-btn confirm-btn-no" onclick="closeConfirm()">Batal</button>
      </div>
    </div>
  </div>

  <script>
  // Dropdown
  function toggleDropdown() {
    document.getElementById('userChipWrap')?.classList.toggle('open');
  }
  document.addEventListener('click', e => {
    const wrap = document.getElementById('userChipWrap');
    if(wrap && !wrap.contains(e.target)) wrap.classList.remove('open');
  });

  // Search filter
  function filterProducts() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(card => {
      const match = (card.dataset.name || '').includes(q);
      card.style.display = match ? '' : 'none';
    });
  }

  // Delete
  let _deleteTarget = null;
  function confirmDeleteProduct(id, btn) {
    _deleteTarget = { id, btn };
    document.getElementById('confirmModal').classList.add('visible');
    document.getElementById('confirmYes').onclick = () => doDelete();
  }

  function closeConfirm() {
    document.getElementById('confirmModal').classList.remove('visible');
    _deleteTarget = null;
  }

  function doDelete() {
    if(!_deleteTarget) return;
    const { id, btn } = _deleteTarget;
    const card = btn.closest('.product-card');

    closeConfirm();

    // Animate removal
    if(card) {
      card.style.transition = 'all 0.4s ease';
      card.style.opacity = '0';
      card.style.transform = 'translateX(40px)';
      setTimeout(() => card.remove(), 400);
    }

    showToast('🗑️ Produk berhasil dihapus');

    // API call
    fetch('<?= $base ?>seller/delete-product', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ product_id: id })
    })
    .then(r => r.json())
    .then(res => {
      if(!res.success) {
        showToast('❌ Gagal menghapus: ' + (res.error || ''), 'error');
        // Reload to restore
        setTimeout(() => location.reload(), 1500);
      }
    })
    .catch(() => {});
  }

  // Toast
  let _toastTimer;
  function showToast(msg, type) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.style.background = type === 'error' ? 'rgba(224,82,82,0.92)' : 'rgba(42,157,143,0.92)';
    t.classList.add('visible');
    clearTimeout(_toastTimer);
    _toastTimer = setTimeout(() => t.classList.remove('visible'), 2800);
  }
  </script>
</body>
</html>
