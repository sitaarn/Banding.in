/**
 * ============================================
 * aboutus.js - Logika Halaman About Us
 * Banding.in - Perbandingan Harga E-commerce
 * ============================================
 * 
 * Menangani:
 * - Dynamic navbar (tampilkan dropdown user jika login, tombol login jika guest)
 * - Toggle dropdown menu
 * - Proses logout
 */

/** Update navbar sesuai status login (dari localStorage) */
function updateNav() {
    const navButtons = document.getElementById('navButtons');
    const isLoggedIn = localStorage.getItem('loggedIn') === 'true';
    const userName = localStorage.getItem('userName') || 'User';
    const userEmail = localStorage.getItem('userEmail') || 'user@example.com';

    /** Generate inisial dari nama. Contoh: "John Doe" → "JD" */
    function getInitials(name) {
      return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
    }

    // Jika login: tampilkan user chip dengan dropdown menu
    if (isLoggedIn) {
      navButtons.innerHTML = `
        <div class="user-chip-wrap" id="userChipWrap">
          <div class="user-chip" onclick="toggleDropdown()">
            <div class="user-avatar">${getInitials(userName)}</div>
            <span class="user-name">${userName}</span>
            <div class="user-online"></div>
            <span class="user-chevron">▾</span>
          </div>
          <div class="user-dropdown">
            <div class="dropdown-info">
              <div class="dropdown-info-name">${userName}</div>
              <div class="dropdown-info-label">${userEmail}</div>
            </div>
            <a class="dropdown-item" href="/bandingin/profile">
              <span class="dropdown-icon"><i class="fa-solid fa-circle-user"></i></span> Profil Saya
            </a>
            <a class="dropdown-item" href="/bandingin/list">
              <span class="dropdown-icon"><i class="fa-solid fa-magnifying-glass"></i></span> Cari Produk
            </a>
            <div class="dropdown-item logout" onclick="doLogout()">
              <span class="dropdown-icon"><i class="fa-solid fa-right-from-bracket"></i></span> Keluar
            </div>
          </div>
        </div>
        <button class="nav-btn" onclick="window.location.href='/bandingin/landing'">Home</button>
      `;
    } else {
      // Jika guest: tampilkan tombol Login dan Home
      navButtons.innerHTML = `
        <button class="nav-btn" onclick="window.location.href='login.html'">Login</button>
        <button class="nav-btn" onclick="window.location.href='/bandingin/landing'">Home</button>
      `;
    }
  }

  /** Toggle buka/tutup dropdown user */
  function toggleDropdown() {
    document.getElementById('userChipWrap').classList.toggle('open');
  }

  /** Logout: hapus data login dari localStorage dan reload halaman */
  function doLogout() {
    localStorage.removeItem('loggedIn');
    localStorage.removeItem('userName');
    localStorage.removeItem('userEmail');
    window.location.reload();
  }

  /** Tutup dropdown saat klik di luar area dropdown */
  document.addEventListener('click', function(e) {
    const wrap = document.getElementById('userChipWrap');
    if (wrap && !wrap.contains(e.target)) {
      wrap.classList.remove('open');
    }
  });

  /** Saat DOM ready: update navbar berdasarkan status login */
  window.addEventListener('DOMContentLoaded', updateNav);