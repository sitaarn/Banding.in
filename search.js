// STATUS LOGIN

const isLoggedIn = localStorage.getItem('loggedIn') === 'true';
const userName   = localStorage.getItem('userName') || '';

function getInitials(n) {
  return n.split(' ').slice(0, 2).map(w => w[0].toUpperCase()).join('');
}

// RENDER NAV sesuai status login

const navLinks = document.getElementById('navLinks');

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
    <a class="nav-btn" href="aboutus.html">About Us</a>
  `;
} else {
  navLinks.innerHTML = `
    <button class="nav-btn" onclick="window.location.href='login.html'">Login</button>
    <button class="nav-btn" onclick="window.location.href='aboutus.html'">About Us</button>
  `;
}

// TAMPILAN AWAL sesuai status login
// Sudah login  → mulai di landing, klik buka search
// Belum login  → langsung ke search

if (!isLoggedIn) {
  document.getElementById('landing').classList.add('hidden');
  document.getElementById('search').classList.remove('hidden');
  window.addEventListener('load', () => document.getElementById('searchInput').focus());
}


// NAVIGASI ANTAR HALAMAN

function goToSearch() {
  document.getElementById('landing').classList.add('hidden');
  document.getElementById('search').classList.remove('hidden');
  setTimeout(() => document.getElementById('searchInput').focus(), 300);
}

function goToLanding() {
  if (isLoggedIn) {
    document.getElementById('search').classList.add('hidden');
    document.getElementById('landing').classList.remove('hidden');
    document.getElementById('resultsArea').style.display  = 'none';
    document.getElementById('mostSearched').style.display = 'block';
    document.getElementById('searchInput').value = '';
  } else {
    window.location.href = 'index.html';
  }
}

// USER CHIP — dropdown & logout

function toggleDropdown() {
  document.getElementById('userChipWrap').classList.toggle('open');
}

function doLogout() {
  localStorage.removeItem('loggedIn');
  localStorage.removeItem('userName');
  localStorage.removeItem('userEmail');
  window.location.href = 'index.html';
}

document.addEventListener('click', function(e) {
  const wrap = document.getElementById('userChipWrap');
  if (wrap && !wrap.contains(e.target)) wrap.classList.remove('open');
});

// PLATFORM FILTER

function togglePlatform(btn) {
  const p = btn.dataset.platform;
  if (p === 'all') {
    document.querySelectorAll('.pf-btn').forEach(b => b.classList.add('active'));
  } else {
    btn.classList.toggle('active');
    const allActive = [...document.querySelectorAll('.pf-btn[data-platform!="all"]')]
      .every(b => b.classList.contains('active'));
    document.querySelector('.pf-btn[data-platform="all"]').classList.toggle('active', allActive);
  }
}

// MOCK DATA

const mockData = {
  platforms: {
    tokopedia: { color: '#42b549', label: 'Tokopedia' },
    shopee:    { color: '#ee4d2d', label: 'Shopee' },
    lazada:    { color: '#0f146b', label: 'Lazada' },
    blibli:    { color: '#0095d9', label: 'Blibli' }
  },
  products: [
    { name: 'iPhone 15',      prices: { tokopedia: 12999000, shopee: 12749000, lazada: 12899000, blibli: 13100000 } },
    { name: 'Samsung Galaxy', prices: { tokopedia:  8499000, shopee:  8299000, lazada:  8350000, blibli:  8600000 } },
    { name: 'Nike Air Max',   prices: { tokopedia:  1850000, shopee:  1799000, lazada:  1920000, blibli:  1875000 } },
    { name: 'Laptop Asus',    prices: { tokopedia:  9999000, shopee:  9750000, lazada:  9850000, blibli: 10200000 } },
    { name: 'AirPods Pro',    prices: { tokopedia:  3499000, shopee:  3299000, lazada:  3350000, blibli:  3600000 } },
    { name: 'Xiaomi Redmi',   prices: { tokopedia:  2199000, shopee:  2099000, lazada:  2150000, blibli:  2299000 } },
    { name: 'Tas Ransel',     prices: { tokopedia:   350000, shopee:   299000, lazada:   320000, blibli:   375000 } },
    { name: 'PS5',            prices: { tokopedia:  8999000, shopee:  8799000, lazada:  8850000, blibli:  9100000 } }
  ]
};


// SEARCH LOGIC

function fillSearch(q) {
  document.getElementById('searchInput').value = q;
  doSearch();
}

function doSearch() {
  const query = document.getElementById('searchInput').value.trim();
  if (!query) return;

  const area = document.getElementById('resultsArea');
  const ms   = document.getElementById('mostSearched');

  ms.style.display   = 'none';
  area.style.display = 'flex';
  area.innerHTML     = '<div class="loading-dots"><div class="dot"></div><div class="dot"></div><div class="dot"></div></div>';

  setTimeout(() => {
    const activePlatforms = [...document.querySelectorAll('.pf-btn[data-platform!="all"].active')]
      .map(b => b.dataset.platform);

    if (!activePlatforms.length) {
      area.innerHTML = '<div style="text-align:center;color:rgba(58,80,104,.5);font-size:.82rem;">Pilih minimal satu platform dulu ya 😊</div>';
      return;
    }

    const match    = mockData.products.find(p => p.name.toLowerCase().includes(query.toLowerCase())) || mockData.products[0];
    const entries  = activePlatforms.map(p => ({ platform: p, price: match.prices[p] })).sort((a, b) => a.price - b.price);
    const minPrice = entries[0].price;

    area.innerHTML = `<div class="results-grid">${
      entries.map(e => `
        <div class="result-card">
          <div class="result-left">
            <span class="result-platform" style="background:${mockData.platforms[e.platform].color}">${mockData.platforms[e.platform].label}</span>
            <span class="result-name">${match.name}</span>
          </div>
          <div style="display:flex;align-items:center">
            <span class="result-price ${e.price === minPrice ? 'cheapest' : ''}">Rp ${e.price.toLocaleString('id-ID')}</span>
            ${e.price === minPrice ? '<span class="badge-cheapest">TERMURAH</span>' : ''}
          </div>
        </div>`
      ).join('')
    }</div>`;
  }, 900);
}

document.addEventListener('keydown', e => {
  if (e.key === 'Enter' && !document.getElementById('search').classList.contains('hidden')) doSearch();
});