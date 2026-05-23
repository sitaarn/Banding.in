<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Keuntungan Menjadi Seller - <?= APP_NAME ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Bergabung menjadi seller di Bandingin dan jangkau jutaan pembeli. Gratis listing, analytics, dan banyak keuntungan lainnya.">
  <link rel="icon" href="./public/images/logo-b.png" type="image/png">
  <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="./public/css/seller.css">
</head>
<body>

  <!-- Navigation -->
  <nav class="benefits-nav">
    <a href="<?= BASE_URL ?>landing" class="nav-logo">
      <i class="fa-solid fa-scale-balanced"></i> <?= APP_NAME ?>
    </a>
    <a href="<?= BASE_URL ?>login?mode=seller" class="nav-cta">
      <i class="fa-solid fa-store"></i> Daftar Seller
    </a>
  </nav>

  <!-- Hero Section -->
  <section class="hero-section">
    <div class="hero-particles">
      <div class="particle"></div>
      <div class="particle"></div>
      <div class="particle"></div>
      <div class="particle"></div>
      <div class="particle"></div>
    </div>
    <div class="hero-content">
      <div class="hero-badge">
        <i class="fa-solid fa-rocket"></i> Platform #1 Perbandingan Harga
      </div>
      <h1>Jual Produk Anda di <span class="gradient-text"><?= APP_NAME ?></span></h1>
      <p class="hero-subtitle">Jangkau jutaan pembeli yang sedang mencari produk terbaik dengan harga terbaik. Listing gratis, tanpa biaya tersembunyi.</p>
      <div class="hero-actions">
        <a href="<?= BASE_URL ?>login?mode=seller" class="btn-primary-hero">
          <i class="fa-solid fa-store"></i> Mulai Berjualan
        </a>
        <a href="#benefits" class="btn-secondary-hero">
          <i class="fa-solid fa-arrow-down"></i> Pelajari Lebih Lanjut
        </a>
      </div>
      <div class="hero-stats">
        <div class="stat-item">
          <span class="stat-number" data-count="10000">10,000+</span>
          <span class="stat-label">Pengguna Aktif</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
          <span class="stat-number" data-count="500">500+</span>
          <span class="stat-label">Seller Terdaftar</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
          <span class="stat-number" data-count="4">4</span>
          <span class="stat-label">Platform E-commerce</span>
        </div>
      </div>
    </div>
  </section>

  <!-- Benefits Section -->
  <section class="benefits-section" id="benefits">
    <div class="section-header">
      <span class="section-tag">Keuntungan</span>
      <h2>Mengapa Menjadi Seller di <?= APP_NAME ?>?</h2>
      <p>Nikmati berbagai keuntungan eksklusif yang kami tawarkan</p>
    </div>

    <div class="benefits-grid">
      <div class="benefit-card" data-aos="fade-up">
        <div class="benefit-icon">
          <i class="fa-solid fa-globe"></i>
        </div>
        <h3>Jangkauan Luas</h3>
        <p>Produk Anda akan tampil di platform perbandingan harga terbesar. Jutaan pengguna akan melihat produk Anda setiap hari.</p>
      </div>

      <div class="benefit-card" data-aos="fade-up">
        <div class="benefit-icon">
          <i class="fa-solid fa-money-bill-wave"></i>
        </div>
        <h3>100% Gratis</h3>
        <p>Tidak ada biaya listing, tidak ada biaya berlangganan. Cukup daftar dan mulai upload produk Anda sekarang juga.</p>
      </div>

      <div class="benefit-card" data-aos="fade-up">
        <div class="benefit-icon">
          <i class="fa-solid fa-chart-line"></i>
        </div>
        <h3>Tingkatkan Penjualan</h3>
        <p>Pembeli yang datang ke Bandingin sudah siap membeli. Konversi penjualan Anda akan meningkat secara signifikan.</p>
      </div>

      <div class="benefit-card" data-aos="fade-up">
        <div class="benefit-icon">
          <i class="fa-solid fa-bolt"></i>
        </div>
        <h3>Mudah & Cepat</h3>
        <p>Proses listing produk sangat mudah. Cukup isi nama, pilih platform, kategori, dan link produk — selesai dalam hitungan detik.</p>
      </div>

      <div class="benefit-card" data-aos="fade-up">
        <div class="benefit-icon">
          <i class="fa-solid fa-shield-halved"></i>
        </div>
        <h3>Terpercaya</h3>
        <p>Bandingin sudah dipercaya oleh ribuan pengguna. Brand awareness toko Anda akan meningkat dengan tampil di platform kami.</p>
      </div>

      <div class="benefit-card" data-aos="fade-up">
        <div class="benefit-icon">
          <i class="fa-solid fa-headset"></i>
        </div>
        <h3>Support 24/7</h3>
        <p>Tim support kami siap membantu Anda kapan saja. Kami pastikan pengalaman berjualan Anda selalu menyenangkan.</p>
      </div>
    </div>
  </section>

  <!-- How It Works -->
  <section class="how-section">
    <div class="section-header">
      <span class="section-tag">Cara Kerja</span>
      <h2>3 Langkah Mudah</h2>
      <p>Mulai berjualan di Bandingin hanya dengan 3 langkah sederhana</p>
    </div>

    <div class="steps-container">
      <div class="step-card">
        <div class="step-number">01</div>
        <div class="step-content">
          <h3>Daftar Akun Seller</h3>
          <p>Buat akun seller gratis dengan mengisi informasi dasar toko Anda.</p>
        </div>
        <div class="step-icon">
          <i class="fa-solid fa-user-plus"></i>
        </div>
      </div>

      <div class="step-connector">
        <div class="connector-line"></div>
        <div class="connector-dot"></div>
      </div>

      <div class="step-card">
        <div class="step-number">02</div>
        <div class="step-content">
          <h3>Upload Produk</h3>
          <p>Tambahkan produk Anda lengkap dengan link ke platform e-commerce.</p>
        </div>
        <div class="step-icon">
          <i class="fa-solid fa-cloud-arrow-up"></i>
        </div>
      </div>

      <div class="step-connector">
        <div class="connector-line"></div>
        <div class="connector-dot"></div>
      </div>

      <div class="step-card">
        <div class="step-number">03</div>
        <div class="step-content">
          <h3>Produk Tampil</h3>
          <p>Produk Anda langsung tampil dan bisa ditemukan oleh jutaan pembeli.</p>
        </div>
        <div class="step-icon">
          <i class="fa-solid fa-rocket"></i>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="cta-section">
    <div class="cta-content">
      <h2>Siap Meningkatkan Penjualan?</h2>
      <p>Bergabung dengan ratusan seller lainnya yang sudah merasakan manfaat berjualan di Bandingin</p>
      <a href="<?= BASE_URL ?>login?mode=seller" class="btn-cta">
        <i class="fa-solid fa-store"></i> Daftar Sebagai Seller — Gratis!
      </a>
    </div>
  </section>

  <!-- Footer -->
  <footer class="benefits-footer">
    <p>&copy; <?= date('Y') ?> <?= APP_NAME ?> — Platform Perbandingan Harga</p>
  </footer>

  <script>
    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    });

    // Intersection Observer for card animations
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry, index) => {
        if (entry.isIntersecting) {
          setTimeout(() => {
            entry.target.classList.add('animate-in');
          }, index * 100);
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.benefit-card, .step-card').forEach(card => {
      observer.observe(card);
    });
  </script>
</body>
</html>
