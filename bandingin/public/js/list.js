/**
 * ============================================
 * list.js - Logika Halaman Pencarian & Daftar Produk
 * Banding.in - Perbandingan Harga E-commerce
 * ============================================
 * 
 * File ini adalah INTI FITUR UTAMA aplikasi:
 * - Halaman Search: cari produk, pilih platform, lihat preview harga
 * - Halaman Listing: daftar semua hasil, filter, sort, pagination
 * - Favorit: toggle simpan/hapus favorit via AJAX
 * - Report: laporkan produk bermasalah
 * - Price slider: filter berdasarkan range harga
 * - Login modal: prompt login untuk fitur yang butuh autentikasi
 * 
 * Data produk di-fetch dari API PHP (/models/get_products.php)
 */

// ═══════════════════════════════════════════
//  DATA & FETCH PRODUK
// ═══════════════════════════════════════════

/** Konfigurasi platform yang didukung */
var mockData = {
  platforms: {
    tokopedia: { color: '#42b549', label: 'Tokopedia' },
    lazada: { color: '#0f146b', label: 'Lazada' },
    blibli: { color: '#0095d9', label: 'Blibli' }
  },
  products: [] // Akan diisi setelah fetch API
};

/**
 * Fetch semua data produk dari backend PHP.
 * Data di-grouping per nama produk, dengan harga & link per platform.
 * Setelah selesai, cek URL parameter ?q= untuk auto-search.
 */
fetch('/bandingin/models/get_products.php')
  .then(res => res.json())
  .then(data => {
    const grouped = {};
    data.forEach(item => {
      const productName = item.product_name;
      if (!grouped[productName]) {
        // Tentukan emoji berdasarkan kategori
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
          prices: {},  // Harga per platform { tokopedia: 1500000, lazada: 1600000, ... }
          links: {}    // Link per platform { tokopedia: 'https://...', lazada: '...', ... }
        };
      }

      // Simpan harga dan link per platform
      if (item.platform_name && item.price) {
        const platform = item.platform_name.trim().toLowerCase();
        grouped[productName].prices[platform] = parseFloat(item.price);
        grouped[productName].links[platform] = item.link || '#';
      }
    });
    mockData.products = Object.values(grouped);
    console.log('mockData loaded:', mockData);

    // Auto-search jika ada parameter ?q= di URL
    const urlParams = new URLSearchParams(window.location.search);
    const query = urlParams.get('q');
    if (query) {
      goToListing(query);
    }
  })
  .catch(err => console.error("Error fetching data:", err));

// ═══════════════════════════════════════════
//  LOAD FAVORIT USER
// ═══════════════════════════════════════════

let userFavorites = []; // Array { product_id, platform } favorit user

/** Jika user login, fetch data favorit dari backend */
if (typeof APP_IS_LOGGED_IN !== 'undefined' && APP_IS_LOGGED_IN) {
  fetch('/bandingin/favorites')
    .then(res => res.json())
    .then(result => {
      if (result.success && result.data) {
        userFavorites = result.data.map(f => ({ product_id: f.product_id, platform: f.platform }));
      }
    })
    .catch(err => console.error("Error fetching favorites:", err));
}

// ═══════════════════════════════════════════
//  KONSTANTA & UTILITIES
// ═══════════════════════════════════════════

const PF_COLORS = { tokopedia: '#42b549', lazada: '#0f146b', blibli: '#0095d9' };
const PF_LABELS = { tokopedia: 'Tokopedia', lazada: 'Lazada', blibli: 'Blibli' };
const PF_RATINGS = {
  tokopedia: { star: '4.9', count: '2.341' },
  lazada: { star: '4.7', count: '956' },
  blibli: { star: '4.6', count: '612' }
};

/** Cari satu produk pertama yang cocok dengan query */
function findProduct(query) {
  return mockData.products.find(
    p => p.name.toLowerCase().includes(query.toLowerCase())
  ) || mockData.products[0];
}

/** Cari SEMUA produk yang cocok (semua kata harus ada di nama produk) */
function findProducts(query) {
  const words = query.toLowerCase().split(/\s+/).filter(w => w.length > 0);
  return mockData.products.filter(p => {
    const name = p.name.toLowerCase();
    return words.every(w => name.includes(w));
  });
}

/** Format harga ke singkatan. Contoh: 1500000 → "Rp 1.5jt" */
function formatPrice(v) {
  if (v === 0) return 'Rp 0';
  if (v >= 1000000) return 'Rp ' + (v / 1000000).toFixed(v % 1000000 === 0 ? 0 : 1) + 'jt';
  return 'Rp ' + (v / 1000).toFixed(0) + 'rb';
}

// ═══════════════════════════════════════════
//  STATE LISTING
// ═══════════════════════════════════════════

let lsCurrentQuery = '';   // Query pencarian saat ini
let lsSort = 'cheapest';   // Mode sorting: cheapest / expensive
let lsActivePf = ['tokopedia', 'lazada', 'blibli']; // Platform yang aktif
let lsPriceMin = 0;         // Filter harga minimum
let lsPriceMax = 25000000;  // Filter harga maksimum
let lsCurrentPage = 1;      // Halaman pagination saat ini
const lsItemsPerPage = 12;  // Jumlah item per halaman
let isLoggedIn = false;      // Status login (dari localStorage)

// ═══════════════════════════════════════════
//  NAVIGASI SEARCH ↔ LISTING
// ═══════════════════════════════════════════

/** Pindah dari halaman search ke halaman listing. Reset semua filter. */
function goToListing(query, activePlatforms = null) {
  lsCurrentQuery = query;
  if (activePlatforms && activePlatforms.length > 0) {
    lsActivePf = activePlatforms;
  } else {
    lsActivePf = ['tokopedia', 'lazada', 'blibli'];
  }
  lsSort = 'cheapest';
  lsPriceMin = 0;
  lsPriceMax = 25000000;
  lsCurrentPage = 1;

  // Update UI sidebar filter
  document.querySelectorAll('.ls-platform-row').forEach(r => {
    if (lsActivePf.includes(r.dataset.pf)) {
      r.classList.add('on');
    } else {
      r.classList.remove('on');
    }
  });
  document.querySelectorAll('.ls-sort-row').forEach(r => r.classList.remove('on'));

  const sortCheapest = document.querySelector('.ls-sort-row[data-sort="cheapest"]');
  if (sortCheapest) sortCheapest.classList.add('on');

  const sliderMin = document.getElementById('sliderMin');
  const sliderMax = document.getElementById('sliderMax');
  if (sliderMin) sliderMin.value = 0;
  if (sliderMax) sliderMax.value = 25000000;

  updateSlider();

  // Sembunyikan search, tampilkan listing
  const searchEl = document.getElementById('search');
  const listingEl = document.getElementById('listing');
  if (searchEl) searchEl.classList.add('hidden');
  if (listingEl) listingEl.classList.remove('hidden');

  renderListing();
}

/** Kembali ke halaman search dari listing */
function backToSearch() {
  document.getElementById('listing').classList.add('hidden');
  document.getElementById('search').classList.remove('hidden');
}

// ═══════════════════════════════════════════
//  HALAMAN SEARCH
// ═══════════════════════════════════════════

/** Toggle platform button di halaman search */
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

/** Isi input search dan langsung jalankan pencarian */
function fillSearch(q) {
  document.getElementById('searchInput').value = q;
  doSearch();
}

/**
 * Proses pencarian produk.
 * Tampilkan loading → cari produk → tampilkan preview hasil (maks 4 item).
 * Preview berisi harga dari platform aktif, diurutkan termurah.
 */
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

  // Delay 900ms untuk efek loading
  setTimeout(() => {
    const ap = [...document.querySelectorAll('.pf-btn.active')]
      .map(b => b.dataset.platform)
      .filter(p => p !== 'all');

    if (!ap.length) {
      area.innerHTML = '<div style="text-align:center;color:rgba(58,80,104,.5);font-size:.82rem;">Pilih minimal satu platform dulu ya 😊</div>';
      return;
    }

    const matches = findProducts(query);
    if (!matches.length) {
      area.innerHTML = '<div style="text-align:center;color:rgba(58,80,104,.5);font-size:.82rem;">Produk tidak ditemukan 😊</div>';
      return;
    }

    // Kumpulkan semua harga dari semua produk yang cocok
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
    const preview = entries.slice(0, 4); // Tampilkan 4 hasil teratas

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
              ${e.price === minPrice ? `<span class="badge-cheapest">${LANG.badge_cheapest}</span>` : ''}
            </div>
          </div>`).join('')}
        <div style="text-align:right;padding-top:8px;">
          <button onclick="goToListing('${query.replace(/'/g, "\\'")}', ['${ap.join("','")}'])" style="padding:8px 20px;border-radius:999px;background:var(--primary);color:var(--bg-mid);border:none;font-family:'DM Sans',sans-serif;font-size:.75rem;font-weight:500;cursor:pointer;letter-spacing:.04em;transition:all .2s;" onmouseover="this.style.background='var(--primary)'" onmouseout="this.style.background='var(--primary)'">${LANG.see_all_results.replace('%d', entries.length)}</button>
        </div>
      </div>`;
  }, 900);
}

// ═══════════════════════════════════════════
//  FAVORIT (Toggle simpan/hapus via AJAX)
// ═══════════════════════════════════════════

/** Cek status login dari localStorage */
function checkLoginStatus() {
  isLoggedIn = localStorage.getItem('loggedIn') === 'true';
  return isLoggedIn;
}

/** Tampilkan toast notification (hijau = sukses, merah = error) */
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

/**
 * Toggle favorit produk (simpan/hapus).
 * Jika belum login → tampilkan login modal.
 * Jika login → kirim request ke backend → update UI.
 */
async function toggleSave(btn, e, id, platform) {
  e.stopPropagation();

  // Cek login
  if (typeof APP_IS_LOGGED_IN !== 'undefined' && !APP_IS_LOGGED_IN) {
    showLoginModal();
    return;
  }

  btn.disabled = true;
  const originalText = btn.textContent;
  btn.textContent = '⏳';
  try {
    const response = await fetch('/bandingin/favorit/toggle', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ product_id: id, platform: platform })
    });
    const result = await response.json();
    if (result.success) {
      btn.classList.toggle('saved');
      btn.textContent = btn.classList.contains('saved') ? '♥' : '♡';
      
      // Update state favorit lokal
      if (btn.classList.contains('saved')) {
        userFavorites.push({ product_id: id, platform: platform });
      } else {
        userFavorites = userFavorites.filter(f => !(f.product_id == id && f.platform == platform));
      }

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

// ═══════════════════════════════════════════
//  HALAMAN LISTING (Render semua hasil)
// ═══════════════════════════════════════════

/**
 * Render halaman listing:
 * 1. Cari produk yang cocok
 * 2. Kumpulkan harga per platform
 * 3. Filter harga (min/max)
 * 4. Sort (termurah/termahal)
 * 5. Pagination
 * 6. Guest: batasi 5 item + prompt login
 */
function renderListing() {
  const main = document.getElementById('lsMain');
  const matches = findProducts(lsCurrentQuery);
  if (!matches.length) {
    main.innerHTML = `
      <div class="ls-topbar">
        <div class="ls-query-info">
          <div class="ls-query-title">${lsCurrentQuery}</div>
          <div class="ls-query-meta">${LANG.no_products_found}</div>
        </div>
        <button class="ls-back-btn" onclick="backToSearch()">← ${LANG.search_again}</button>
      </div>
      <div class="ls-empty">
        <div class="ls-empty-icon">🔍</div>
        <div class="ls-empty-text">${LANG.no_products_hint}</div>
      </div>`;
    return;
  }

  // Kumpulkan SEMUA harga dari SEMUA produk yang cocok
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

  // Filter berdasarkan range harga
  items = items.filter(i => i.price >= lsPriceMin && i.price <= lsPriceMax);

  // Sort
  if (lsSort === 'cheapest') items.sort((a, b) => a.price - b.price);
  if (lsSort === 'expensive') items.sort((a, b) => b.price - a.price);

  if (!items.length) {
    main.innerHTML = `
      <div class="ls-topbar">
        <div class="ls-query-info">
          <div class="ls-query-title">${lsCurrentQuery}</div>
          <div class="ls-query-meta">${LANG.no_products_price}</div>
        </div>
        <button class="ls-back-btn" onclick="backToSearch()">← ${LANG.search_again}</button>
      </div>
      <div class="ls-empty">
        <div class="ls-empty-icon">🔍</div>
        <div class="ls-empty-text">${LANG.no_products_price_hint}</div>
      </div>`;
    return;
  }

  // Cari harga termurah (untuk badge)
  const globalMin = Math.min(...items.map(i => i.price));

  // Guest: batasi 5 item + tampilkan prompt login
  let showLoginPrompt = false;
  let originalLength = items.length;
  if (typeof APP_IS_LOGGED_IN !== 'undefined' && !APP_IS_LOGGED_IN) {
    if (items.length > 5) {
      items = items.slice(0, 5);
      showLoginPrompt = true;
    }
  }

  // Pagination
  const totalPages = Math.ceil(items.length / lsItemsPerPage);
  if (lsCurrentPage > totalPages) lsCurrentPage = totalPages || 1;
  const startIndex = (lsCurrentPage - 1) * lsItemsPerPage;
  const paginatedItems = items.slice(startIndex, startIndex + lsItemsPerPage);

  // Generate HTML card untuk setiap produk
  const cards = paginatedItems.map((item, idx) => {
    const isCheapest = item.price === globalMin && globalMin > 0;
    const visitLink = item.link && item.link !== '#' ? item.link : '#';
    const visitTarget = visitLink !== '#' ? 'target="_blank"' : '';
    const visitOnclick = visitLink === '#' ? 'return false' : '';

    // Cek apakah produk ini ada di favorit user
    const isFav = userFavorites.some(f => f.product_id == item.id && f.platform == item.platform);
    const favButton = (!IS_SELLER && (typeof IS_SUPERADMIN === 'undefined' || !IS_SUPERADMIN)) ? `<button class="ls-btn-save ${isFav ? 'saved' : ''}" onclick="toggleSave(this, event, ${item.id}, '${item.platform}')" title="Save">${isFav ? '♥' : '♡'}</button>` : '';

    const rank = startIndex + idx + 1; // Nomor urut global (across pages)

    return `
      <div class="ls-card">
        <div class="ls-card-rank ${isCheapest ? 'gold' : ''}">${rank}</div>
        <div class="ls-card-body">
          <div class="ls-card-top">
            <div>
              <div class="ls-card-name">${item.name}</div>
              <div class="ls-card-sub">${item.sub}</div>
            </div>
            <div class="ls-card-badges">
              ${isCheapest ? `<span class="ls-badge ls-badge-cheap">${LANG.cheapest}</span>` : ''}
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
              ${favButton}
              ${!IS_SELLER ? `<button class="ls-btn-report" onclick="openReportModal(event, ${item.id}, '${item.platform}')" title="${LANG.report}"><i class="fa-solid fa-flag"></i></button>` : ''}
              <a class="ls-btn-visit" href="${visitLink}" ${visitTarget} onclick="${visitOnclick}">${LANG.visit} →</a>
            </div>
          </div>
        </div>
      </div>`;
  }).join('');

  // Generate pagination HTML
  let paginationHTML = '';
  if (totalPages > 1 && !showLoginPrompt) {
    paginationHTML = '<div class="ls-pagination">';
    if (lsCurrentPage > 1) {
      paginationHTML += `<button class="ls-page-btn" onclick="lsChangePage(${lsCurrentPage - 1})">Prev</button>`;
    }
    let maxPages = 5;
    let startPage = Math.max(1, lsCurrentPage - Math.floor(maxPages / 2));
    let endPage = startPage + maxPages - 1;
    if (endPage > totalPages) {
      endPage = totalPages;
      startPage = Math.max(1, endPage - maxPages + 1);
    }
    for (let i = startPage; i <= endPage; i++) {
      if (i === lsCurrentPage) {
        paginationHTML += `<button class="ls-page-btn active">${i}</button>`;
      } else {
        paginationHTML += `<button class="ls-page-btn" onclick="lsChangePage(${i})">${i}</button>`;
      }
    }
    if (lsCurrentPage < totalPages) {
      paginationHTML += `<button class="ls-page-btn" onclick="lsChangePage(${lsCurrentPage + 1})">Next</button>`;
    }
    paginationHTML += '</div>';
  }

  // Prompt login untuk guest (jika hasil > 5)
  let loginPromptHTML = '';
  if (showLoginPrompt) {
    loginPromptHTML = `
      <div class="ls-login-prompt">
        <div class="ls-login-icon">🔒</div>
        <div class="ls-login-text">${LANG.login_to_see_more.replace('%d', originalLength - 5)}</div>
        <button class="ls-login-btn" onclick="goToLogin()">Login / Register</button>
      </div>
    `;
  }

  // Render ke DOM
  main.innerHTML = `
    <div class="ls-topbar">
      <div class="ls-query-info">
        <div class="ls-query-title">${lsCurrentQuery}</div>
        <div class="ls-query-meta">
          <strong>${showLoginPrompt ? originalLength : items.length} ${LANG.results_found}</strong> · ${LANG.updated_just_now}
        </div>
      </div>
      <button class="ls-back-btn" onclick="backToSearch()">← ${LANG.search_again}</button>
    </div>
    ${cards}
    ${paginationHTML}
    ${loginPromptHTML}`;
}

/** Pindah halaman pagination dan scroll ke atas */
function lsChangePage(page) {
  lsCurrentPage = page;
  renderListing();
  document.querySelector('.ls-main-col').scrollTo({ top: 0, behavior: 'smooth' });
}

// ═══════════════════════════════════════════
//  SIDEBAR CONTROLS (Filter & Sort)
// ═══════════════════════════════════════════

/** Toggle platform on/off di sidebar listing */
function lsTogglePf(row) {
  const pf = row.dataset.pf;
  row.classList.toggle('on');
  lsActivePf = row.classList.contains('on') ? [...lsActivePf, pf] : lsActivePf.filter(p => p !== pf);
  lsCurrentPage = 1;
  renderListing();
}

/** Set mode sorting (cheapest/expensive) */
function lsSetSort(row) {
  document.querySelectorAll('.ls-sort-row').forEach(r => r.classList.remove('on'));
  row.classList.add('on');
  lsSort = row.dataset.sort;
  lsCurrentPage = 1;
  renderListing();
}

// ═══════════════════════════════════════════
//  LOGIN MODAL
// ═══════════════════════════════════════════

/** Tampilkan modal login (untuk fitur yang butuh autentikasi) */
function showLoginModal(title = 'Save to Favorites', icon = '❤️', sub = 'Log in or create a free account to continue.') {
  const tEl = document.getElementById('loginModalTitle');
  const iEl = document.getElementById('loginModalIcon');
  const sEl = document.getElementById('loginModalSub');
  if (tEl) tEl.innerText = title;
  if (iEl) iEl.innerHTML = icon;
  if (sEl) sEl.innerText = sub;
  document.getElementById('loginModal').classList.add('visible');
}

/** Tutup modal login */
function closeLoginModal() { document.getElementById('loginModal').classList.remove('visible'); }

/** Mock login via localStorage (legacy, sekarang pakai PHP session) */
function mockLogin() {
  isLoggedIn = true;
  localStorage.setItem('loggedIn', 'true');
  closeLoginModal();
  showToast('✓ Login berhasil! Sekarang kamu bisa simpan favorit.');
}

// ═══════════════════════════════════════════
//  REPORT MODAL (Laporkan produk bermasalah)
// ═══════════════════════════════════════════

let currentReportProduct = null;   // ID produk yang dilaporkan
let currentReportPlatform = null;  // Platform produk yang dilaporkan

/** Toggle input teks custom saat pilih alasan "other" */
function toggleReportReason(checkbox) {
  const textInput = document.getElementById('reportReasonText');
  if (checkbox.checked) {
    textInput.style.display = 'block';
    textInput.focus();
  } else {
    textInput.style.display = 'none';
  }
}

/** Buka modal report (cek login dulu) */
function openReportModal(e, productId, platform) {
  e.stopPropagation();
  if (typeof APP_IS_LOGGED_IN !== 'undefined' && !APP_IS_LOGGED_IN) {
    showLoginModal('Report Product', '<i class="fa-solid fa-flag text-red"></i>', 'Log in to report a product.');
    return;
  }
  currentReportProduct = productId;
  currentReportPlatform = platform;
  
  // Reset form report
  document.getElementById('reportReasonText').value = '';
  document.getElementById('reportReasonText').style.display = 'none';
  const checkboxes = document.querySelectorAll('input[name="report_reason_check"]');
  checkboxes.forEach(cb => cb.checked = false);
  
  document.getElementById('reportModal').classList.add('visible');
}

/** Tutup modal report */
function closeReportModal() {
  document.getElementById('reportModal').classList.remove('visible');
  currentReportProduct = null;
  currentReportPlatform = null;
}

/**
 * Kirim laporan produk ke backend via AJAX.
 * Mengumpulkan semua alasan yang dipilih (checkbox),
 * mapping platform name ke ID, lalu POST ke API.
 */
async function submitReport() {
  let reasons = [];
  const selectedCheckboxes = document.querySelectorAll('input[name="report_reason_check"]:checked');
  
  if (selectedCheckboxes.length === 0) {
    showToast('Harap pilih alasan pelaporan.', true);
    return;
  }
  
  // Kumpulkan alasan dari checkbox yang dipilih
  selectedCheckboxes.forEach(cb => {
    if (cb.value === 'other') {
      const otherReason = document.getElementById('reportReasonText').value.trim();
      if (otherReason) {
        reasons.push(otherReason);
      }
    } else {
      reasons.push(cb.value);
    }
  });

  if (reasons.length === 0) {
    showToast('Harap isi alasan pelaporan lainnya.', true);
    return;
  }

  let reason = reasons.join(', ');
  
  const btn = document.getElementById('btnSubmitReport');
  btn.disabled = true;
  btn.textContent = '⏳';
  
  // Mapping nama platform ke ID di database
  const platformMap = {
    'tokopedia': 2,
    'lazada': 3,
    'blibli': 4
  };
  const mappedPlatformId = platformMap[currentReportPlatform] || 1;

  try {
    const response = await fetch('/bandingin/product/report', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ product_id: currentReportProduct, platform_id: mappedPlatformId, reason: reason })
    });
    const result = await response.json();
    if (result.success) {
      showToast(result.message || 'Laporan terkirim.');
      closeReportModal();
    } else {
      showToast(result.error || 'Gagal mengirim laporan', true);
    }
  } catch (error) {
    showToast('Terjadi kesalahan.', true);
  } finally {
    btn.disabled = false;
    btn.textContent = LANG.submit_report || 'Ajukan Laporan';
  }
}

// ═══════════════════════════════════════════
//  PRICE SLIDER (Filter range harga)
// ═══════════════════════════════════════════

/** Inisialisasi dual range slider untuk filter harga */
function initSlider() {
  const minEl = document.getElementById('sliderMin');
  const maxEl = document.getElementById('sliderMax');
  if (!minEl || !maxEl) return;
  updateSlider();
  // Cegah min > max dan sebaliknya
  minEl.addEventListener('input', () => {
    if (+minEl.value > +maxEl.value - 500000) minEl.value = +maxEl.value - 500000;
    updateSlider();
  });
  maxEl.addEventListener('input', () => {
    if (+maxEl.value < +minEl.value + 500000) maxEl.value = +minEl.value + 500000;
    updateSlider();
  });
}

/** Update tampilan slider fill (bar berwarna antara min dan max) */
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

/** Terapkan filter harga dan re-render listing */
function lsApplyPrice() {
  lsPriceMin = +document.getElementById('sliderMin').value;
  lsPriceMax = +document.getElementById('sliderMax').value;
  lsCurrentPage = 1;
  renderListing();
}

// ═══════════════════════════════════════════
//  KEYBOARD SHORTCUT & GLOBAL UI
// ═══════════════════════════════════════════

/** Enter di halaman search → jalankan pencarian */
document.addEventListener('keydown', e => {
  const searchEl = document.getElementById('search');
  if (e.key === 'Enter' && searchEl && !searchEl.classList.contains('hidden')) {
    doSearch();
  }
});

/** Toggle dropdown user */
function toggleDropdown() { document.getElementById('userChipWrap').classList.toggle('open'); }

/** Tutup dropdown saat klik di luar */
document.addEventListener('click', function (e) {
  const wrap = document.getElementById('userChipWrap');
  if (wrap && !wrap.contains(e.target)) wrap.classList.remove('open');
});

/** Logout: hapus localStorage dan redirect */
function doLogout() {
  localStorage.removeItem('loggedIn');
  window.location.href = '/bandingin/logout';
}

/** Redirect ke halaman login */
function goToLogin() { window.location.href = '/bandingin/login'; }

/** Redirect ke halaman listing dengan query parameter */
function goToListingPage(query) { window.location.href = '/bandingin/list?q=' + query; }

/** Saat DOM ready: inisialisasi slider */
window.addEventListener('DOMContentLoaded', initSlider);