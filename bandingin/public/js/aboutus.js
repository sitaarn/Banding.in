  function updateNav() {
    const navButtons = document.getElementById('navButtons');
    const isLoggedIn = localStorage.getItem('loggedIn') === 'true';
    const userName = localStorage.getItem('userName') || 'User';
    const userEmail = localStorage.getItem('userEmail') || 'user@example.com';

    function getInitials(name) {
      return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
    }

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
            <a class="dropdown-item" href="http://localhost/bandingin/profile">
              <span class="dropdown-icon">👤</span> Profil Saya
            </a>
            <a class="dropdown-item" href="http://localhost/bandingin/search">
              <span class="dropdown-icon">🔍</span> Cari Produk
            </a>
            <div class="dropdown-item logout" onclick="doLogout()">
              <span class="dropdown-icon">↩</span> Keluar
            </div>
          </div>
        </div>
        <button class="nav-btn" onclick="window.location.href='http://localhost/bandingin/landing'">Home</button>
      `;
    } else {
      navButtons.innerHTML = `
        <button class="nav-btn" onclick="window.location.href='login.html'">Login</button>
        <button class="nav-btn" onclick="window.location.href='http://localhost/bandingin/landing'">Home</button>
      `;
    }
  }

  function toggleDropdown() {
    document.getElementById('userChipWrap').classList.toggle('open');
  }

  function doLogout() {
    localStorage.removeItem('loggedIn');
    localStorage.removeItem('userName');
    localStorage.removeItem('userEmail');
    window.location.reload();
  }

  document.addEventListener('click', function(e) {
    const wrap = document.getElementById('userChipWrap');
    if (wrap && !wrap.contains(e.target)) {
      wrap.classList.remove('open');
    }
  });

  window.addEventListener('DOMContentLoaded', updateNav);