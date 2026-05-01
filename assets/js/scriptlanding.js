function goToSearch() {
  window.location.href = 'search.html';
}

function goToLogin() {
  localStorage.setItem('redirectAfterLogin', window.location.href);
  window.location.href = 'login.html';
}

function toggleDropdown() {
  const wrap = document.getElementById('userChipWrap');
  if (wrap) wrap.classList.toggle('open');
}

function doLogout() {
  localStorage.removeItem('loggedIn');
  localStorage.removeItem('userName');
  localStorage.removeItem('userEmail');
  window.location.reload();
}

document.addEventListener('DOMContentLoaded', function() {
  const navLinks = document.getElementById('navLinks');
  const isLoggedIn = localStorage.getItem('loggedIn') === 'true';
  const userName = localStorage.getItem('userName') || '';

  function getInitials(name) {
    return name.split(' ').slice(0, 2).map(w => w[0]?.toUpperCase() || '').join('');
  }

  if (isLoggedIn && userName) {
    navLinks.innerHTML = `
      <div class="user-chip-wrap" id="userChipWrap">
        <div class="user-chip" onclick="toggleDropdown()">
          <div class="user-avatar">${getInitials(userName)}</div>
          <span class="user-name">${userName}</span>
          <div class="user-online"></div>
          <span class="user-chevron">▼</span>
        </div>
        <div class="user-dropdown">
          <div class="dropdown-info">
            <div class="dropdown-info-name">${userName}</div>
            <div class="dropdown-info-label">Sedang login ✓</div>
          </div>
          <div class="dropdown-item logout" onclick="doLogout()">
            <span class="dropdown-icon">🚪</span> Logout
          </div>
        </div>
      </div>
      <button class="nav-btn" onclick="window.location.href='aboutus.html'">About Us</button>
    `;
  } else {
    navLinks.innerHTML = `
      <button class="nav-btn" onclick="goToLogin()">Login</button>
      <button class="nav-btn" onclick="window.location.href='aboutus.html'">About Us</button>
    `;
  }

  document.addEventListener('click', function(e) {
    const wrap = document.getElementById('userChipWrap');
    if (wrap && !wrap.contains(e.target)) {
      wrap.classList.remove('open');
    }
  });
});