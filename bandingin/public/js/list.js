/* list.js Banding.in - Versi Final dengan Platform, Link, Nomor Urut & Favorit */

/* DATA */
var mockData = {
  platforms: {
    tokopedia: { color: '#42b549', label: 'Tokopedia' },
    shopee: { color: '#ee4d2d', label: 'Shopee' },
    lazada: { color: '#0f146b', label: 'Lazada' },
    blibli: { color: '#0095d9', label: 'Blibli' }
  },
  products: []
};

// Menggunakan endpoint PHP yang sudah jalan dan terhubung ke MySQL
fetch('http://localhost/bandingin/models/get_products.php')
  .then(res => res.json())
  .then(data => {
    const grouped = {};
    data.forEach(item => {
      const productName = item.product_name;
      if (!grouped[productName]) {
        let emoji = '📦';
        if (item.category === 'Smartphone') emoji = '📱';
        else if (item.category === 'Laptop') emoji = '💻';
        else if (item.category === 'Sepatu') emoji = '👟';
        else if (item.category === 'Audio') emoji = '🎧';
        else if (item.category === 'Furniture') emoji = '🪑';

        grouped[productName] = {
          name: productName,
          emoji: emoji,
          id: item.id,
          sub: item.category || 'Uncategorized',
          prices: {},
          links: {}  // <-- menyimpan link per platform
        };
      }

      if (item.platform_name && item.price) {
        const platform = item.platform_name.trim().toLowerCase();
        grouped[productName].prices[platform] = parseFloat(item.price);
        grouped[productName].links[platform] = item.link || '#'; // <-- simpan link
      }
    });
    mockData.products = Object.values(grouped);
    console.log('mockData loaded:', mockData);

    /* BACA PARAMETER URL (Dijalankan setelah data berhasil dimuat) */
    const urlParams = new URLSearchParams(window.location.search);
    const query = urlParams.get('q');
    if (query) {
      goToListing(query);
    }
  })
  .catch(err => console.error("Error fetching data:", err));

const PF_COLORS = { tokopedia: '#42b549', shopee: '#ee4d2d', lazada: '#0f146b', blibli: '#0095d9' };
const PF_LABELS = { tokopedia: 'Tokopedia', shopee: 'Shopee', lazada: 'Lazada', blibli: 'Blibli' };
const PF_RATINGS = {
  tokopedia: { star: '4.9', count: '2.341' },
  shopee: { star: '4.8', count: '1.820' },
  lazada: { star: '4.7', count: '956' },
  blibli: { star: '4.6', count: '612' }
};

/* UTILS */
function findProduct(query) {
  return mockData.products.find(
    p => p.name.toLowerCase().includes(query.toLowerCase())
  ) || mockData.products[0];
}

/* Mencari SEMUA produk yang cocok dengan query */
function findProducts(query) {
  const words = query.toLowerCase().split(/\s+/).filter(w => w.length > 0);
  return mockData.products.filter(p => {
    const name = p.name.toLowerCase();
    return words.every(w => name.includes(w));
  });
}

function formatPrice(v) {
  if (v === 0) return 'Rp 0';
  if (v >= 1000000) return 'Rp ' + (v / 1000000).toFixed(v % 1000000 === 0 ? 0 : 1) + 'jt';
  return 'Rp ' + (v / 1000).toFixed(0) + 'rb';
}

/* STATE LISTING */
let lsCurrentQuery = '';
let lsSort = 'cheapest';
let lsActivePf = ['tokopedia', 'shopee', 'lazada', 'blibli'];
let lsPriceMin = 0;
let lsPriceMax = 25000000;
let isLoggedIn = false;

/* NAVIGASI */
function goToListing(query) {
  lsCurrentQuery = query;
  lsActivePf = ['tokopedia', 'shopee', 'lazada', 'blibli'];
  lsSort = 'cheapest';
  lsPriceMin = 0;
  lsPriceMax = 25000000;

  document.querySelectorAll('.ls-platform-row').forEach(r => r.classList.add('on'));
  document.querySelectorAll('.ls-sort-row').forEach(r => r.classList.remove('on'));

  const sortCheapest = document.querySelector('.ls-sort-row[data-sort="cheapest"]');
  if (sortCheapest) sortCheapest.classList.add('on');

  const sliderMin = document.getElementById('sliderMin');
  const sliderMax = document.getElementById('sliderMax');
  if (sliderMin) sliderMin.value = 0;
  if (sliderMax) sliderMax.value = 25000000;

  updateSlider();

  const searchEl = document.getElementById('search');
  const listingEl = document.getElementById('listing');
  if (searchEl) searchEl.classList.add('hidden');
  if (listingEl) listingEl.classList.remove('hidden');

  renderListing();
}

function backToSearch() {
  document.getElementById('listing').classList.add('hidden');
  document.getElementById('search').classList.remove('hidden');
}

/* SEARCH PAGE */
function togglePlatform(btn) {
  const p = btn.dataset.platform;
  if (p === 'all') {
    document.querySelectorAll('.pf-btn').forEach(b => b.classList.add('active'));
  } else {
    btn.classList.toggle('active');
    const pfBtns = [...document.querySelectorAll('.pf-btn')].filter(b => b.dataset.platform !== 'all');
    const allActive = pfBtns.every(b => b.classList.contains('active'));
    document.querySelector('.pf-btn[data-platform="all"]').classList.toggle('active', allActive);
  }
}

function fillSearch(q) {
  document.getElementById('searchInput').value = q;
  doSearch();
}

function doSearch() {
  const query = document.getElementById('searchInput').value.trim();
  if (!query) return;

  const area = document.getElementById('resultsArea');
  const ms = document.getElementById('mostSearched');
  if (ms) ms.style.display = 'none';
  if (area) {
    area.style.display = 'flex';
    area.innerHTML = '<div class="loading-dots"><div class="dot"></div><div class="dot"></div><div class="dot"></div></div>';
  }

  setTimeout(() => {
    const ap = [...document.querySelectorAll('.pf-btn.active')]
      .map(b => b.dataset.platform)
      .filter(p => p !== 'all');

    if (!ap.length) {
      area.innerHTML = '<div style="text-align:center;color:rgba(58,80,104,.5);font-size:.82rem;">Pilih minimal satu platform dulu ya \uD83D\uDE0A</div>';
      return;
    }

    /* Cari SEMUA produk yang cocok */
    const matches = findProducts(query);
    if (!matches.length) {
      area.innerHTML = '<div style="text-align:center;color:rgba(58,80,104,.5);font-size:.82rem;">Produk tidak ditemukan \uD83D\uDE0A</div>';
      return;
    }

    /* Kumpulkan semua harga dari semua produk yang cocok */
    let entries = [];
    matches.forEach(match => {
      ap.filter(p => match.prices[p] && match.prices[p] > 0).forEach(p => {
        entries.push({ platform: p, price: match.prices[p], name: match.name });
      });
    });
    entries.sort((a, b) => a.price - b.price);

    if (!entries.length) {
      area.innerHTML = '<div style="text-align:center;color:rgba(58,80,104,.5);font-size:.82rem;">Harga tidak tersedia untuk platform yang dipilih.</div>';
      return;
    }

    const minPrice = entries[0].price;
    const preview = entries.slice(0, 4);

    area.innerHTML = `
      <div class="results-grid">
        ${preview.map(e => `
          <div class="result-card">
            <div class="result-left">
              <span class="result-platform" style="background:${mockData.platforms[e.platform]?.color || '#888'}">${mockData.platforms[e.platform]?.label || e.platform}</span>
              <span class="result-name">${e.name}</span>
            </div>
            <div style="display:flex;align-items:center">
              <span class="result-price ${e.price === minPrice ? 'cheapest' : ''}">Rp ${e.price.toLocaleString('id-ID')}</span>
              ${e.price === minPrice ? '<span class="badge-cheapest">TERMURAH</span>' : ''}
            </div>
          </div>`).join('')}
        <div style="text-align:right;padding-top:8px;">
          <button onclick="goToListing('${query.replace(/'/g, "\\'")}')" style="padding:8px 20px;border-radius:999px;background:var(--text-dark);color:white;border:none;font-family:'DM Sans',sans-serif;font-size:.75rem;font-weight:500;cursor:pointer;letter-spacing:.04em;transition:all .2s;" onmouseover="this.style.background='var(--blue)'" onmouseout="this.style.background='var(--text-dark)'">Lihat Semua ${entries.length} Hasil →</button>
        </div>
      </div>`;
  }, 900);
}

/* FAVORITE FUNCTIONS */
function checkLoginStatus() {
  isLoggedIn = localStorage.getItem('loggedIn') === 'true';
  return isLoggedIn;
}

function showToast(msg, isError = false) {
  let toast = document.getElementById('customToast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'customToast';
    toast.style.cssText = `
      position: fixed; bottom: 80px; left: 50%; transform: translateX(-50%) translateY(20px);
      background: #2a9d8f; color: white; padding: 10px 22px; border-radius: 999px;
      font-size: .78rem; font-weight: 500; z-index: 1000; opacity: 0;
      transition: opacity 0.3s ease, transform 0.3s ease; pointer-events: none;
    `;
    document.body.appendChild(toast);
  }
  toast.textContent = msg;
  toast.style.opacity = '1';
  toast.style.transform = 'translateX(-50%) translateY(0)';
  if (isError) toast.style.background = '#dc3545';
  else toast.style.background = '#2a9d8f';
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(-50%) translateY(20px)';
  }, 2000);
}

async function toggleSave(btn, e, id, platform) {
  e.stopPropagation();

  if (typeof APP_IS_LOGGED_IN !== 'undefined' && !APP_IS_LOGGED_IN) {
    showLoginModal();
    return;
  }

  btn.disabled = true;
  const originalText = btn.textContent;
  btn.textContent = '⏳';
  try {
    const response = await fetch('http://localhost/bandingin/favorit/toggle', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ product_id: id, platform: platform })
    });
    const result = await response.json();
    if (result.success) {
      btn.classList.toggle('saved');
      btn.textContent = btn.classList.contains('saved') ? '♥' : '♡';
      showToast(result.message);
    } else {
      showToast(result.error || 'Gagal', true);
      btn.textContent = originalText;
    }
  } catch (error) {
    showToast('Gagal menyimpan', true);
    btn.textContent = originalText;
  } finally {
    btn.disabled = false;
  }
}

/* LISTING PAGE — RENDER SEMUA PRODUK YANG COCOK */
function renderListing() {
  const main = document.getElementById('lsMain');
  const matches = findProducts(lsCurrentQuery);
  if (!matches.length) {
    main.innerHTML = `
      <div class="ls-topbar">
        <div class="ls-query-info">
          <div class="ls-query-title">${lsCurrentQuery}</div>
          <div class="ls-query-meta">Produk tidak ditemukan</div>
        </div>
        <button class="ls-back-btn" onclick="backToSearch()">\u2190 Cari lagi</button>
      </div>
      <div class="ls-empty">
        <div class="ls-empty-icon">\uD83D\uDD0D</div>
        <div class="ls-empty-text">Tidak ada produk yang cocok.<br>Coba kata kunci lain.</div>
      </div>`;
    return;
  }

  /* Kumpulkan SEMUA harga dari SEMUA produk yang cocok */
  let items = [];
  matches.forEach(match => {
    lsActivePf
      .filter(pf => match.prices[pf] && match.prices[pf] > 0)
      .forEach(pf => {
        items.push({
          name: match.name,
          sub: match.sub || 'Elektronik',
          id: match.id,
          platform: pf,
          price: match.prices[pf],
          link: match.links?.[pf] || '#'
        });
      });
  });

  items = items.filter(i => i.price >= lsPriceMin && i.price <= lsPriceMax);

  if (lsSort === 'cheapest') items.sort((a, b) => a.price - b.price);
  if (lsSort === 'expensive') items.sort((a, b) => b.price - a.price);

  if (!items.length) {
    main.innerHTML = `
      <div class="ls-topbar">
        <div class="ls-query-info">
          <div class="ls-query-title">${lsCurrentQuery}</div>
          <div class="ls-query-meta">Tidak ada hasil di rentang harga ini</div>
        </div>
        <button class="ls-back-btn" onclick="backToSearch()">\u2190 Cari lagi</button>
      </div>
      <div class="ls-empty">
        <div class="ls-empty-icon">\uD83D\uDD0D</div>
        <div class="ls-empty-text">Tidak ada produk di rentang harga ini.<br>Coba sesuaikan filter.</div>
      </div>`;
    return;
  }

  const globalMin = Math.min(...items.map(i => i.price));

  const cards = items.map((item, idx) => {
    const isCheapest = idx === 0;
    const visitLink = item.link && item.link !== '#' ? item.link : '#';
    const visitTarget = visitLink !== '#' ? 'target="_blank"' : '';
    const visitOnclick = visitLink === '#' ? 'return false' : '';

    return `
      <div class="ls-card">
        <div class="ls-card-rank ${idx === 0 ? 'gold' : ''}">${idx + 1}</div>
        <div class="ls-card-body">
          <div class="ls-card-top">
            <div>
              <div class="ls-card-name">${item.name}</div>
              <div class="ls-card-sub">${item.sub}</div>
            </div>
            <div class="ls-card-badges">
              ${isCheapest ? '<span class="ls-badge ls-badge-cheap">Termurah</span>' : ''}
            </div>
          </div>
          <div class="ls-platform-tag">
            <div class="ls-pf-logo" style="background:${PF_COLORS[item.platform] || '#888'}">${item.platform[0].toUpperCase()}</div>
            ${PF_LABELS[item.platform] || item.platform}
          </div>
          <div class="ls-card-foot">
            <div>
              <span class="ls-card-price ${isCheapest ? 'best' : ''}">Rp ${item.price.toLocaleString('id-ID')}</span>
            </div>
            <div class="ls-card-actions">
              <button class="ls-btn-save" onclick="toggleSave(this, event, ${item.id}, '${item.platform}')" title="Simpan ke Favorit">\u2661</button>
              <a class="ls-btn-visit" href="${visitLink}" ${visitTarget} onclick="${visitOnclick}">Kunjungi \u2192</a>
            </div>
          </div>
        </div>
      </div>`;
  }).join('');

  main.innerHTML = `
    <div class="ls-topbar">
      <div class="ls-query-info">
        <div class="ls-query-title">${lsCurrentQuery}</div>
        <div class="ls-query-meta">
          <strong>${items.length} hasil</strong> ditemukan \u00B7 diperbarui barusan
        </div>
      </div>
      <button class="ls-back-btn" onclick="backToSearch()">\u2190 Cari lagi</button>
    </div>
    ${cards}`;
}

/* SIDEBAR CONTROLS */
function lsTogglePf(row) {
  const pf = row.dataset.pf;
  row.classList.toggle('on');
  lsActivePf = row.classList.contains('on') ? [...lsActivePf, pf] : lsActivePf.filter(p => p !== pf);
  renderListing();
}
function lsSetSort(row) {
  document.querySelectorAll('.ls-sort-row').forEach(r => r.classList.remove('on'));
  row.classList.add('on');
  lsSort = row.dataset.sort;
  renderListing();
}

/* LOGIN MODAL */
function showLoginModal() { document.getElementById('loginModal').classList.add('visible'); }
function closeLoginModal() { document.getElementById('loginModal').classList.remove('visible'); }
function mockLogin() {
  isLoggedIn = true;
  localStorage.setItem('loggedIn', 'true');
  closeLoginModal();
  showToast('✓ Login berhasil! Sekarang kamu bisa simpan favorit.');
}

/* PRICE SLIDER */
function initSlider() {
  const minEl = document.getElementById('sliderMin');
  const maxEl = document.getElementById('sliderMax');
  if (!minEl || !maxEl) return;
  updateSlider();
  minEl.addEventListener('input', () => {
    if (+minEl.value > +maxEl.value - 500000) minEl.value = +maxEl.value - 500000;
    updateSlider();
  });
  maxEl.addEventListener('input', () => {
    if (+maxEl.value < +minEl.value + 500000) maxEl.value = +minEl.value + 500000;
    updateSlider();
  });
}
function updateSlider() {
  const minEl = document.getElementById('sliderMin');
  const maxEl = document.getElementById('sliderMax');
  const fill = document.getElementById('sliderFill');
  if (!minEl || !maxEl || !fill) return;
  const total = 25000000;
  const pctL = (+minEl.value / total) * 100;
  const pctR = (+maxEl.value / total) * 100;
  fill.style.left = pctL + '%';
  fill.style.width = (pctR - pctL) + '%';
  document.getElementById('priceMinLabel').textContent = formatPrice(+minEl.value);
  document.getElementById('priceMaxLabel').textContent = formatPrice(+maxEl.value);
}
function lsApplyPrice() {
  lsPriceMin = +document.getElementById('sliderMin').value;
  lsPriceMax = +document.getElementById('sliderMax').value;
  renderListing();
}

/* KEYBOARD SHORTCUT */
document.addEventListener('keydown', e => {
  const searchEl = document.getElementById('search');
  if (e.key === 'Enter' && searchEl && !searchEl.classList.contains('hidden')) {
    doSearch();
  }
});

/* DROPDOWN & LOGOUT */
function toggleDropdown() { document.getElementById('userChipWrap').classList.toggle('open'); }
document.addEventListener('click', function (e) {
  const wrap = document.getElementById('userChipWrap');
  if (wrap && !wrap.contains(e.target)) wrap.classList.remove('open');
});
function doLogout() {
  localStorage.removeItem('loggedIn');
  window.location.href = 'http://localhost/bandingin/logout';
}
function goToLogin() { window.location.href = 'http://localhost/bandingin/login'; }
function goToListingPage(query) { window.location.href = 'http://localhost/bandingin/list?q=' + query; }

window.addEventListener('DOMContentLoaded', initSlider);