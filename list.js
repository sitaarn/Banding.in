/* listing.js Banding.in */

/* DATA */
const mockData = {
  platforms: {
    tokopedia: { color: '#42b549', label: 'Tokopedia' },
    shopee:    { color: '#ee4d2d', label: 'Shopee'    },
    lazada:    { color: '#0f146b', label: 'Lazada'    },
    blibli:    { color: '#0095d9', label: 'Blibli'    }
  },
  products: [
    { name: 'iPhone 15',          emoji: '📱', sub: '128GB · Garansi Resmi',   prices: { tokopedia: 12999000, shopee: 12749000, lazada: 12899000, blibli: 13100000 } },
    { name: 'Samsung Galaxy S24', emoji: '📱', sub: '256GB · Official Store',  prices: { tokopedia:  8499000, shopee:  8299000, lazada:  8350000, blibli:  8600000 } },
    { name: 'Nike Air Max',       emoji: '👟', sub: 'Size 40-44 · Original',   prices: { tokopedia:  1850000, shopee:  1799000, lazada:  1920000, blibli:  1875000 } },
    { name: 'Laptop Asus ROG',    emoji: '💻', sub: '16GB RAM · RTX 4060',     prices: { tokopedia:  9999000, shopee:  9750000, lazada:  9850000, blibli: 10200000 } },
    { name: 'AirPods Pro',        emoji: '🎧', sub: '2nd Gen · MagSafe',       prices: { tokopedia:  3499000, shopee:  3299000, lazada:  3350000, blibli:  3600000 } },
    { name: 'Xiaomi Redmi Note',  emoji: '📱', sub: '8GB/256GB · NFC',         prices: { tokopedia:  2199000, shopee:  2099000, lazada:  2150000, blibli:  2299000 } },
    { name: 'Tas Ransel',         emoji: '🎒', sub: 'Anti-Air · 40L',          prices: { tokopedia:   350000, shopee:   299000, lazada:   320000, blibli:   375000 } },
    { name: 'PS5 Slim',           emoji: '🎮', sub: 'Disc Edition · Baru',     prices: { tokopedia:  8999000, shopee:  8799000, lazada:  8850000, blibli:  9100000 } }
  ]
};

const PF_COLORS  = { tokopedia: '#42b549', shopee: '#ee4d2d', lazada: '#0f146b', blibli: '#0095d9' };
const PF_LABELS  = { tokopedia: 'Tokopedia', shopee: 'Shopee', lazada: 'Lazada', blibli: 'Blibli' };
const PF_RATINGS = {
  tokopedia: { star: '4.9', count: '2.341' },
  shopee:    { star: '4.8', count: '1.820' },
  lazada:    { star: '4.7', count: '956'   },
  blibli:    { star: '4.6', count: '612'   }
};

/* UTILS */
function findProduct(query) {
  return mockData.products.find(
    p => p.name.toLowerCase().includes(query.toLowerCase())
  ) || mockData.products[0];
}

function formatPrice(v) {
  if (v === 0) return 'Rp 0';
  if (v >= 1000000) return 'Rp ' + (v / 1000000).toFixed(v % 1000000 === 0 ? 0 : 1) + 'jt';
  return 'Rp ' + (v / 1000).toFixed(0) + 'rb';
}

/* STATE LISTING */
let lsCurrentQuery = '';
let lsSort         = 'cheapest';
let lsActivePf     = ['tokopedia', 'shopee', 'lazada', 'blibli'];
let lsPriceMin     = 0;
let lsPriceMax     = 25000000;
let isLoggedIn     = false;

/* NAVIGASI */
function goToListing(query) {
  lsCurrentQuery = query;

  // Reset sidebar ke default
  lsActivePf = ['tokopedia', 'shopee', 'lazada', 'blibli'];
  lsSort     = 'cheapest';
  lsPriceMin = 0;
  lsPriceMax = 25000000;
  document.querySelectorAll('.ls-platform-row').forEach(r => r.classList.add('on'));
  document.querySelectorAll('.ls-sort-row').forEach(r => r.classList.remove('on'));
  document.querySelector('.ls-sort-row[data-sort="cheapest"]').classList.add('on');
  document.getElementById('sliderMin').value = 0;
  document.getElementById('sliderMax').value = 25000000;
  updateSlider();

  // Pindah halaman
  document.getElementById('search').classList.add('hidden');
  document.getElementById('listing').classList.remove('hidden');
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
    const pfBtns    = [...document.querySelectorAll('.pf-btn')].filter(b => b.dataset.platform !== 'all');
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
  const ms   = document.getElementById('mostSearched');
  ms.style.display   = 'none';
  area.style.display = 'flex';
  area.innerHTML = '<div class="loading-dots"><div class="dot"></div><div class="dot"></div><div class="dot"></div></div>';

  setTimeout(() => {
    const ap = [...document.querySelectorAll('.pf-btn.active')]
      .map(b => b.dataset.platform)
      .filter(p => p !== 'all');

    if (!ap.length) {
      area.innerHTML = '<div style="text-align:center;color:rgba(58,80,104,.5);font-size:.82rem;">Pilih minimal satu platform dulu ya 😊</div>';
      return;
    }

    const match    = findProduct(query);
    const entries  = ap.map(p => ({ platform: p, price: match.prices[p] })).sort((a, b) => a.price - b.price);
    const minPrice = entries[0].price;

    area.innerHTML = `
      <div class="results-grid">
        ${entries.map(e => `
          <div class="result-card">
            <div class="result-left">
              <span class="result-platform" style="background:${mockData.platforms[e.platform].color}">${mockData.platforms[e.platform].label}</span>
              <span class="result-name">${match.name}</span>
            </div>
            <div style="display:flex;align-items:center">
              <span class="result-price ${e.price === minPrice ? 'cheapest' : ''}">Rp ${e.price.toLocaleString('id-ID')}</span>
              ${e.price === minPrice ? '<span class="badge-cheapest">TERMURAH</span>' : ''}
            </div>
          </div>`).join('')}
        <div style="text-align:right;padding-top:8px;">
          <button
            onclick="goToListing('${match.name}')"
            style="padding:8px 20px;border-radius:999px;background:var(--text-dark);color:white;border:none;font-family:'DM Sans',sans-serif;font-size:.75rem;font-weight:500;cursor:pointer;letter-spacing:.04em;transition:all .2s;"
            onmouseover="this.style.background='var(--blue)'"
            onmouseout="this.style.background='var(--text-dark)'">
            Lihat Semua Hasil →
          </button>
        </div>
      </div>`;
  }, 900);
}

/* LISTING PAGE — RENDER */
function renderListing() {
  const main  = document.getElementById('lsMain');
  const match = findProduct(lsCurrentQuery);

  // 1. Buat items dari platform aktif
  let items = lsActivePf.map(pf => ({ ...match, platform: pf, price: match.prices[pf] }));

  // 2. Filter rentang harga
  items = items.filter(i => i.price >= lsPriceMin && i.price <= lsPriceMax);

  // 3. Urutkan
  if (lsSort === 'cheapest')  items.sort((a, b) => a.price - b.price);
  if (lsSort === 'expensive') items.sort((a, b) => b.price - a.price);
  if (lsSort === 'rating') {
    const order = { tokopedia: 0, shopee: 1, lazada: 2, blibli: 3 };
    items.sort((a, b) => order[a.platform] - order[b.platform]);
  }

  // 4. Kosong
  if (!items.length) {
    main.innerHTML = `
      <div class="ls-topbar">
        <div class="ls-query-info">
          <div class="ls-query-title">${match.name}</div>
          <div class="ls-query-meta">Tidak ada hasil di rentang harga ini</div>
        </div>
        <button class="ls-back-btn" onclick="backToSearch()">← Cari lagi</button>
      </div>
      <div class="ls-empty">
        <div class="ls-empty-icon">🔍</div>
        <div class="ls-empty-text">Tidak ada produk di rentang harga ini.<br>Coba sesuaikan filter.</div>
      </div>`;
    return;
  }

  // 5. Hitung proporsi mini bar
  const allPrices = lsActivePf.map(pf => match.prices[pf]);
  const globalMin = Math.min(...allPrices);
  const globalMax = Math.max(...allPrices);

  // 6. Render kartu
  const cards = items.map((item, idx) => {
    const isCheapest = item.price === globalMin;
    const origPrice  = Math.round(item.price * 1.12);
    const rating     = PF_RATINGS[item.platform];

    const bars = ['tokopedia', 'shopee', 'lazada', 'blibli'].map(pf => {
      const pct    = Math.round((match.prices[pf] / globalMax) * 100);
      const isBest = match.prices[pf] === globalMin;
      return `
        <div class="ls-minibar">
          <div class="ls-minibar-lbl">${PF_LABELS[pf].slice(0, 4)}</div>
          <div class="ls-minibar-track">
            <div class="ls-minibar-fill ${isBest ? 'best' : ''}" style="width:${pct}%"></div>
          </div>
        </div>`;
    }).join('');

    return `
      <div class="ls-card">
        <div class="ls-card-rank ${idx === 0 ? 'gold' : ''}">${idx + 1}</div>
        <div class="ls-card-img">${item.emoji}</div>
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
            <div class="ls-pf-logo" style="background:${PF_COLORS[item.platform]}">${item.platform[0].toUpperCase()}</div>
            ${PF_LABELS[item.platform]} · ⭐ ${rating.star} (${rating.count})
          </div>
          <div class="ls-minibars">${bars}</div>
          <div class="ls-card-foot">
            <div>
              <span class="ls-card-price ${isCheapest ? 'best' : ''}">Rp ${item.price.toLocaleString('id-ID')}</span>
              ${isCheapest
                ? `<span class="ls-card-orig">Rp ${origPrice.toLocaleString('id-ID')}</span>
                   <span class="ls-card-disc">-12%</span>`
                : ''}
            </div>
            <div class="ls-card-actions">
              <button class="ls-btn-save" onclick="toggleSave(this, event)" title="Simpan ke Favorit">♡</button>
              <a class="ls-btn-visit" href="#" onclick="return false">Kunjungi →</a>
            </div>
          </div>
        </div>
      </div>`;
  }).join('');

  // 7. Inject ke DOM
  main.innerHTML = `
    <div class="ls-topbar">
      <div class="ls-query-info">
        <div class="ls-query-title">${match.name}</div>
        <div class="ls-query-meta">
          <strong>${items.length} hasil</strong> dari ${items.length} platform · diperbarui barusan
        </div>
      </div>
      <button class="ls-back-btn" onclick="backToSearch()">← Cari lagi</button>
    </div>
    ${cards}`;
}

/* SIDEBAR CONTROLS */
function lsTogglePf(row) {
  const pf = row.dataset.pf;
  row.classList.toggle('on');
  lsActivePf = row.classList.contains('on')
    ? [...lsActivePf, pf]
    : lsActivePf.filter(p => p !== pf);
  renderListing();
}

function lsSetSort(row) {
  document.querySelectorAll('.ls-sort-row').forEach(r => r.classList.remove('on'));
  row.classList.add('on');
  lsSort = row.dataset.sort;
  renderListing();
}

/* FAVORITE */
function toggleSave(btn, e) {
  e.stopPropagation();
  if (!isLoggedIn) { showLoginModal(); return; }
  btn.classList.toggle('saved');
  btn.textContent = btn.classList.contains('saved') ? '♥' : '♡';
}

/* LOGIN MODAL */
function showLoginModal()  { document.getElementById('loginModal').classList.add('visible'); }
function closeLoginModal() { document.getElementById('loginModal').classList.remove('visible'); }
function mockLogin() {
  isLoggedIn = true;
  closeLoginModal();
  const t = document.getElementById('toast');
  t.classList.add('visible');
  setTimeout(() => t.classList.remove('visible'), 2500);
}

/* PRICE SLIDER */
function initSlider() {
  const minEl = document.getElementById('sliderMin');
  const maxEl = document.getElementById('sliderMax');
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
  const fill  = document.getElementById('sliderFill');
  const total = 25000000;
  const pctL  = (+minEl.value / total) * 100;
  const pctR  = (+maxEl.value / total) * 100;
  fill.style.left  = pctL + '%';
  fill.style.width = (pctR - pctL) + '%';
  document.getElementById('priceMinLabel').textContent = formatPrice(+minEl.value);
  document.getElementById('priceMaxLabel').textContent = formatPrice(+maxEl.value);
}

function lsApplyPrice() {
  lsPriceMin = +document.getElementById('sliderMin').value;
  lsPriceMax = +document.getElementById('sliderMax').value;
  renderListing();
}

/* COMPARE TRAY */
function clearCompare() {
  document.getElementById('compareTray').classList.remove('visible');
}

/* KEYBOARD SHORTCUT — Enter to search */
document.addEventListener('keydown', e => {
  if (e.key === 'Enter' && !document.getElementById('search').classList.contains('hidden')) {
    doSearch();
  }
});

/* INIT */
window.addEventListener('DOMContentLoaded', initSlider);