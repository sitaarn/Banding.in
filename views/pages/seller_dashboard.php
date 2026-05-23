<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Seller Dashboard - <?= APP_NAME ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="./public/images/logo-b.png" type="image/png">
  <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="./public/css/seller.css">
</head>
<body class="dashboard-body">

  <!-- Sidebar -->
  <aside class="seller-sidebar" id="sidebar">
    <div class="sidebar-header">
      <a href="<?= BASE_URL ?>landing" class="sidebar-logo">
        <i class="fa-solid fa-scale-balanced"></i>
        <span><?= APP_NAME ?></span>
      </a>
    </div>

    <div class="sidebar-user">
      <div class="user-avatar">
        <i class="fa-solid fa-store"></i>
      </div>
      <div class="user-info">
        <span class="user-name"><?= e($_SESSION['nama_lengkap'] ?? 'Seller') ?></span>
        <span class="user-role">Seller Account</span>
      </div>
    </div>

    <nav class="sidebar-nav">
      <a href="<?= BASE_URL ?>seller/dashboard" class="nav-item active">
        <i class="fa-solid fa-grid-2"></i>
        <span>Dashboard</span>
      </a>
      <a href="<?= BASE_URL ?>landing" class="nav-item">
        <i class="fa-solid fa-house"></i>
        <span>Landing Page</span>
      </a>
      <a href="<?= BASE_URL ?>logout" class="nav-item logout-item">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span>Logout</span>
      </a>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="seller-main">
    <!-- Top Bar -->
    <header class="seller-topbar">
      <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fa-solid fa-bars"></i>
      </button>
      <h1 class="topbar-title">Seller Dashboard</h1>
      <div class="topbar-actions">
        <span class="seller-badge">
          <i class="fa-solid fa-circle-check"></i> Seller Aktif
        </span>
      </div>
    </header>

    <!-- Flash Messages -->
    <div class="dashboard-content">
      <?php displayFlashMessage(); ?>

      <!-- Stats Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-card-icon">
            <i class="fa-solid fa-box"></i>
          </div>
          <div class="stat-card-info">
            <span class="stat-card-value"><?= $totalProducts ?? 0 ?></span>
            <span class="stat-card-label">Total Produk</span>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-card-icon platform-icon">
            <i class="fa-solid fa-shop"></i>
          </div>
          <div class="stat-card-info">
            <span class="stat-card-value"><?= count($platforms ?? []) ?></span>
            <span class="stat-card-label">Platform Tersedia</span>
          </div>
        </div>
      </div>

      <!-- Add Product Section -->
      <div class="dashboard-section">
        <div class="section-title">
          <h2><i class="fa-solid fa-plus-circle"></i> Tambah Produk Baru</h2>
        </div>

        <form action="<?= BASE_URL ?>seller/product/add" method="post" class="add-product-form" id="addProductForm">
          <div class="form-grid">
            <div class="form-group">
              <label for="productName">
                <i class="fa-solid fa-tag"></i> Nama Produk
              </label>
              <input type="text" id="productName" name="name" placeholder="Contoh: Apple iPhone 15 128GB" required>
            </div>

            <div class="form-group">
              <label for="productPlatform">
                <i class="fa-solid fa-shop"></i> Platform / E-commerce
              </label>
              <select id="productPlatform" name="platform" required>
                <option value="">Pilih Platform</option>
                <?php if (!empty($platforms)): ?>
                  <?php foreach ($platforms as $platform): ?>
                    <option value="<?= e($platform['name']) ?>"><?= e($platform['name']) ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>

            <div class="form-group">
              <label for="productCategory">
                <i class="fa-solid fa-layer-group"></i> Kategori
              </label>
              <select id="productCategory" name="category" required>
                <option value="">Pilih Kategori</option>
                <option value="Smartphone">Smartphone</option>
                <option value="Laptop">Laptop</option>
                <option value="Audio">Audio</option>
                <option value="Gaming">Gaming</option>
                <option value="Fashion">Fashion</option>
                <option value="Sepatu">Sepatu</option>
                <option value="Furniture">Furniture</option>
                <option value="Elektronik">Elektronik</option>
                <option value="Kesehatan">Kesehatan</option>
                <option value="Makanan">Makanan</option>
                <option value="Lainnya">Lainnya</option>
              </select>
            </div>

            <div class="form-group">
              <label for="productPrice">
                <i class="fa-solid fa-money-bill"></i> Harga (Rp) <span class="optional-label">Opsional</span>
              </label>
              <input type="number" id="productPrice" name="price" placeholder="Contoh: 12499000" min="0">
            </div>

            <div class="form-group full-width">
              <label for="productLink">
                <i class="fa-solid fa-link"></i> Link Produk
              </label>
              <input type="url" id="productLink" name="link" placeholder="https://shopee.co.id/produk-anda" required>
            </div>
          </div>

          <button type="submit" class="btn-add-product">
            <i class="fa-solid fa-cloud-arrow-up"></i> Upload Produk
          </button>
        </form>
      </div>

      <!-- Product List Section -->
      <div class="dashboard-section">
        <div class="section-title">
          <h2><i class="fa-solid fa-boxes-stacked"></i> Produk Saya</h2>
        </div>

        <?php if (empty($products)): ?>
          <div class="empty-state">
            <i class="fa-solid fa-box-open"></i>
            <h3>Belum ada produk</h3>
            <p>Mulai tambahkan produk pertama Anda menggunakan form di atas</p>
          </div>
        <?php else: ?>
          <div class="products-table-wrapper">
            <table class="products-table">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Produk</th>
                  <th>Platform</th>
                  <th>Kategori</th>
                  <th>Harga</th>
                  <th>Link</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $no = 1;
                $displayedProducts = [];
                foreach ($products as $product): 
                  // Avoid duplicate display for same product
                  if (in_array($product['id'], $displayedProducts)) continue;
                  $displayedProducts[] = $product['id'];
                ?>
                  <tr>
                    <td class="td-center"><?= $no++ ?></td>
                    <td>
                      <div class="product-name-cell">
                        <i class="fa-solid fa-cube"></i>
                        <?= e($product['name']) ?>
                      </div>
                    </td>
                    <td>
                      <span class="platform-badge"><?= e($product['platform'] ?? $product['platform_name'] ?? '-') ?></span>
                    </td>
                    <td><?= e($product['category'] ?? '-') ?></td>
                    <td>
                      <?php if (!empty($product['price']) && $product['price'] > 0): ?>
                        Rp <?= number_format($product['price'], 0, ',', '.') ?>
                      <?php else: ?>
                        <span class="text-muted">-</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php $link = $product['link'] ?? $product['price_link'] ?? ''; ?>
                      <?php if (!empty($link)): ?>
                        <a href="<?= e($link) ?>" target="_blank" class="link-btn">
                          <i class="fa-solid fa-external-link-alt"></i> Lihat
                        </a>
                      <?php else: ?>
                        <span class="text-muted">-</span>
                      <?php endif; ?>
                    </td>
                    <td class="td-center">
                      <form action="<?= BASE_URL ?>seller/product/delete" method="post" class="delete-form" 
                            onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <button type="submit" class="btn-delete">
                          <i class="fa-solid fa-trash"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </main>

  <script src="./public/js/seller.js"></script>
</body>
</html>
