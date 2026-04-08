// ========================
// BANDING.IN ADMIN PANEL (JS ONLY)
// ========================

// ---------- DATA ----------
let products = [];
let nextId = 1;
let editingId = null;
let deleteTargetId = null;

const PLATFORM_COLORS = {
  tokopedia: '#42b549',
  shopee: '#ee4d2d',
  lazada: '#0f146b',
  blibli: '#0095d9',
};

// ---------- INITIAL DATA ----------
function loadInitialData() {
  const stored = localStorage.getItem('banding_products');
  if (stored) {
    products = JSON.parse(stored);
    nextId = products.reduce((max, p) => Math.max(max, p.id), 0) + 1;
  } else {
    products = [
      {
        id: 1,
        name: 'Samsung Galaxy S24 Ultra',
        platform: 'tokopedia',
        category: 'smartphone',
        price: 18500000,
        orig_price: 19999000,
        discount: 7,
        rating: 4.9,
        sold: 340,
        emoji: '📱',
        condition: 'baru',
        url: '#',
        img_url: '',
        tags: 'samsung, galaxy',
        description: 'HP flagship',
      },
      {
        id: 2,
        name: 'MacBook Air M2',
        platform: 'shopee',
        category: 'laptop',
        price: 15999000,
        orig_price: 17999000,
        discount: 11,
        rating: 4.8,
        sold: 122,
        emoji: '💻',
        condition: 'baru',
        url: '#',
        tags: 'apple, laptop',
      },
      {
        id: 3,
        name: 'Sony WH-1000XM5',
        platform: 'lazada',
        category: 'audio',
        price: 3899000,
        orig_price: 4999000,
        discount: 22,
        rating: 4.9,
        sold: 892,
        emoji: '🎧',
        condition: 'baru',
        url: '#',
      },
      {
        id: 4,
        name: 'Erigo Pants Casual',
        platform: 'blibli',
        category: 'fashion',
        price: 249000,
        orig_price: 399000,
        discount: 37,
        rating: 4.7,
        sold: 2100,
        emoji: '👖',
        condition: 'baru',
        url: '#',
      },
    ];
    nextId = 5;
  }
  renderEverything();
}

function saveToLocal() {
  localStorage.setItem('banding_products', JSON.stringify(products));
}

// ---------- RENDER ALL UI ----------
function renderEverything() {
  renderProductList();
  updateStatsAndSidebar();
  updatePreview();
}

function renderProductList() {
  const activePlatforms = [...document.querySelectorAll('.ls-platform-row.on')].map((el) => el.dataset.pf);
  const searchTerm = document.getElementById('admSearch')?.value.toLowerCase() || '';
  let filtered = products.filter(
    (p) =>
      activePlatforms.includes(p.platform) &&
      (p.name.toLowerCase().includes(searchTerm) || (p.tags || '').toLowerCase().includes(searchTerm))
  );
  const container = document.getElementById('productListWrap');
  if (!container) return;
  if (filtered.length === 0) {
    container.innerHTML = '<div class="ls-empty">📭 Belum ada produk</div>';
    return;
  }
  container.innerHTML = filtered
    .map(
      (p, idx) => `
      <div class="ls-card adm-product-row" data-name="${p.name.toLowerCase()}" data-platform="${p.platform}">
        <div class="ls-card-rank ${idx === 0 ? 'gold' : ''}">${idx + 1}</div>
        <div class="ls-card-img">${p.emoji || '📦'}</div>
        <div class="ls-card-body">
          <div><strong>${escapeHtml(p.name)}</strong><div style="font-size:12px">${p.platform} · ${
        p.category || 'Lainnya'
      }</div></div>
          <div style="display:flex; gap:8px; margin-top:6px">
            <div class="ls-pf-dot" style="background:${PLATFORM_COLORS[p.platform]}"></div> 
            ${p.rating ? `★ ${p.rating}` : ''} · ${p.sold?.toLocaleString() || 0} terjual
          </div>
        </div>
        <div style="text-align:right">
          <div>
            <span class="ls-card-price">Rp ${p.price.toLocaleString()}</span> 
            ${p.orig_price ? `<span style="text-decoration:line-through;font-size:12px">Rp ${p.orig_price.toLocaleString()}</span>` : ''}
          </div>
          <div class="ls-card-actions" style="margin-top:8px">
            <button class="adm-btn-edit" onclick="editProduct(${p.id})">✏️</button>
            <button class="adm-btn-del" onclick="deleteProduct(${p.id})">🗑️</button>
            <a class="ls-btn-visit" href="${escapeHtml(p.url)}" target="_blank">Lihat ↗</a>
          </div>
        </div>
      </div>
    `
    )
    .join('');
}

function updateStatsAndSidebar() {
  const total = products.length;
  const counts = { tokopedia: 0, shopee: 0, lazada: 0, blibli: 0 };
  let totalPrice = 0;
  products.forEach((p) => {
    counts[p.platform]++;
    totalPrice += p.price;
  });
  const avgPrice = total ? totalPrice / total : 0;

  // update sidebar mini stats
  const statTotal = document.getElementById('statTotal');
  const statTok = document.getElementById('statTok');
  const statShop = document.getElementById('statShop');
  const statLaz = document.getElementById('statLaz');
  const statBli = document.getElementById('statBli');
  if (statTotal) statTotal.innerText = total;
  if (statTok) statTok.innerText = counts.tokopedia;
  if (statShop) statShop.innerText = counts.shopee;
  if (statLaz) statLaz.innerText = counts.lazada;
  if (statBli) statBli.innerText = counts.blibli;

  // stats grid
  const statsGrid = document.getElementById('statsGrid');
  if (statsGrid) {
    statsGrid.innerHTML = `
      <div class="adm-stat-card"><div>📦</div><div style="font-size:1.8rem">${total}</div><div>Total Produk</div></div>
      <div class="adm-stat-card"><div>🟢</div><div style="font-size:1.6rem">${counts.tokopedia}</div><div>Tokopedia</div></div>
      <div class="adm-stat-card"><div>🔴</div><div>${counts.shopee}</div><div>Shopee</div></div>
      <div class="adm-stat-card"><div>🔵</div><div>${counts.lazada}</div><div>Lazada</div></div>
      <div class="adm-stat-card"><div>💙</div><div>${counts.blibli}</div><div>Blibli</div></div>
      <div class="adm-stat-card"><div>💰</div><div>Rp ${Math.round(avgPrice).toLocaleString()}</div><div>Rata Harga</div></div>
    `;
  }

  // bar chart container
  const barDiv = document.getElementById('barChartContainer');
  if (barDiv) {
    const platformList = ['tokopedia', 'shopee', 'lazada', 'blibli'];
    const totalProd = total || 1;
    barDiv.innerHTML =
      `<div class="ls-panel-title">Distribusi Platform</div>` +
      platformList
        .map((pf) => {
          const cnt = counts[pf];
          const pct = Math.round((cnt / totalProd) * 100);
          return `<div class="adm-bar-row">
            <div class="adm-bar-label">${pf}</div>
            <div class="adm-bar-track"><div class="adm-bar-fill" style="width:${pct}%;background:${PLATFORM_COLORS[pf]}"></div></div>
            <div>${pct}%</div>
            <div>${cnt}</div>
          </div>`;
        })
        .join('');
  }
}

// ---------- CRUD OPERATIONS ----------
function submitProduct() {
  const name = document.getElementById('fName').value.trim();
  const platform = document.getElementById('fPlatform').value;
  const category = document.getElementById('fCategory').value;
  const price = parseInt(document.getElementById('fPrice').value);
  const url = document.getElementById('fUrl').value.trim();

  if (!name || !platform || !category || !price || !url) {
    showToast('⚠️ Lengkapi field wajib (*)', 'warn');
    return;
  }

  const productData = {
    id: editingId ? editingId : nextId++,
    name,
    platform,
    category,
    price,
    orig_price: parseInt(document.getElementById('fOrigPrice').value) || 0,
    discount: parseInt(document.getElementById('fDiscount').value) || 0,
    rating: parseFloat(document.getElementById('fRating').value) || null,
    sold: parseInt(document.getElementById('fSold').value) || 0,
    emoji: document.getElementById('fEmoji').value.trim() || '📦',
    condition: document.querySelector('input[name="condition"]:checked')?.value || 'baru',
    url,
    img_url: document.getElementById('fImg').value.trim(),
    tags: document.getElementById('fTags').value.trim(),
    description: document.getElementById('fDesc').value.trim(),
  };

  if (editingId) {
    const index = products.findIndex((p) => p.id === editingId);
    if (index !== -1) products[index] = { ...productData, id: editingId };
    showToast('✏️ Produk diperbarui');
  } else {
    products.push(productData);
    showToast('✓ Produk ditambahkan');
  }

  saveToLocal();
  resetForm();
  renderEverything();
  editingId = null;
  const submitBtn = document.getElementById('submitBtn');
  if (submitBtn) submitBtn.innerHTML = '💾 Simpan Produk';

  // switch to list tab
  const listMenuItem = document.querySelector('.adm-menu-item[data-section="list"]');
  if (listMenuItem) listMenuItem.click();
}

function editProduct(id) {
  const prod = products.find((p) => p.id === id);
  if (!prod) return;
  editingId = id;

  document.getElementById('fName').value = prod.name;
  document.getElementById('fPlatform').value = prod.platform;
  document.getElementById('fCategory').value = prod.category || '';
  document.getElementById('fPrice').value = prod.price;
  document.getElementById('fOrigPrice').value = prod.orig_price || '';
  document.getElementById('fDiscount').value = prod.discount || '';
  document.getElementById('fRating').value = prod.rating || '';
  document.getElementById('fSold').value = prod.sold || '';
  document.getElementById('fEmoji').value = prod.emoji || '';
  document.querySelector(`input[name="condition"][value="${prod.condition}"]`).checked = true;
  document.getElementById('fUrl').value = prod.url;
  document.getElementById('fImg').value = prod.img_url || '';
  document.getElementById('fTags').value = prod.tags || '';
  document.getElementById('fDesc').value = prod.description || '';

  const submitBtn = document.getElementById('submitBtn');
  if (submitBtn) submitBtn.innerHTML = '✏️ Update Produk';
  updatePreview();

  // switch to add section
  const addMenuItem = document.querySelector('.adm-menu-item[data-section="add"]');
  if (addMenuItem) addMenuItem.click();
}

function deleteProduct(id) {
  deleteTargetId = id;
  const modal = document.getElementById('deleteModal');
  if (modal) modal.classList.add('visible');
}

function confirmDelete() {
  if (deleteTargetId) {
    products = products.filter((p) => p.id !== deleteTargetId);
    saveToLocal();
    renderEverything();
    showToast('🗑️ Produk dihapus');
    deleteTargetId = null;
  }
  closeDeleteModal();
}

function closeDeleteModal() {
  const modal = document.getElementById('deleteModal');
  if (modal) modal.classList.remove('visible');
  deleteTargetId = null;
}

function resetForm() {
  document.getElementById('fName').value = '';
  document.getElementById('fPlatform').value = '';
  document.getElementById('fCategory').value = '';
  document.getElementById('fPrice').value = '';
  document.getElementById('fOrigPrice').value = '';
  document.getElementById('fDiscount').value = '';
  document.getElementById('fRating').value = '';
  document.getElementById('fSold').value = '';
  document.getElementById('fEmoji').value = '';
  const baruRadio = document.querySelector('input[name="condition"][value="baru"]');
  if (baruRadio) baruRadio.checked = true;
  document.getElementById('fUrl').value = '';
  document.getElementById('fImg').value = '';
  document.getElementById('fTags').value = '';
  document.getElementById('fDesc').value = '';
  editingId = null;
  const submitBtn = document.getElementById('submitBtn');
  if (submitBtn) submitBtn.innerHTML = '💾 Simpan Produk';
  updatePreview();
}

// ---------- UI HELPERS ----------
function updatePreview() {
  const name = document.getElementById('fName').value.trim() || 'Nama Produk';
  const platform = document.getElementById('fPlatform').value;
  const categorySelect = document.getElementById('fCategory');
  const category = categorySelect.options[categorySelect.selectedIndex]?.text || 'Kategori';
  const price = parseInt(document.getElementById('fPrice').value) || 0;
  const orig = parseInt(document.getElementById('fOrigPrice').value) || 0;
  const disc = parseInt(document.getElementById('fDiscount').value) || 0;
  const rating = parseFloat(document.getElementById('fRating').value) || 0;
  const sold = parseInt(document.getElementById('fSold').value) || 0;
  const emoji = document.getElementById('fEmoji').value.trim() || '📱';

  const prevName = document.getElementById('prevName');
  const prevImg = document.getElementById('prevImg');
  const prevSub = document.getElementById('prevSub');
  const prevRating = document.getElementById('prevRating');
  const prevSold = document.getElementById('prevSold');
  const prevPrice = document.getElementById('prevPrice');
  const prevOrig = document.getElementById('prevOrig');
  const prevDisc = document.getElementById('prevDisc');

  if (prevName) prevName.innerText = name;
  if (prevImg) prevImg.innerText = emoji;
  if (prevSub) prevSub.innerHTML = `${platform || '?'} · ${category}`;
  if (prevRating) prevRating.innerHTML = rating ? `★ ${rating}` : '★ —';
  if (prevSold) prevSold.innerHTML = sold ? `${sold.toLocaleString()} terjual` : '— terjual';
  if (prevPrice) prevPrice.innerHTML = price ? `Rp ${price.toLocaleString()}` : 'Rp —';
  if (prevOrig) {
    if (orig && orig > price) prevOrig.innerHTML = `Rp ${orig.toLocaleString()}`;
    else prevOrig.innerHTML = '';
  }
  if (prevDisc) {
    if (disc) prevDisc.innerHTML = `-${disc}%`;
    else prevDisc.innerHTML = '';
  }
}

function filterProductList() {
  renderProductList();
}

function admTogglePf(el) {
  el.classList.toggle('on');
  renderProductList();
  updateStatsAndSidebar();
}

function switchSection(el) {
  document.querySelectorAll('.adm-menu-item').forEach((i) => i.classList.remove('on'));
  el.classList.add('on');
  const target = el.dataset.section;
  document.querySelectorAll('.adm-section').forEach((s) => s.classList.add('hidden'));
  const targetId = `sec${target.charAt(0).toUpperCase() + target.slice(1)}`;
  const targetSection = document.getElementById(targetId);
  if (targetSection) targetSection.classList.remove('hidden');
  if (target === 'stats') updateStatsAndSidebar();
  if (target === 'list') renderProductList();
}

function toggleDropdown() {
  const wrap = document.getElementById('userChipWrap');
  if (wrap) wrap.classList.toggle('open');
}

function doLogout() {
  showToast('Logout simulasi');
  window.location.href = '#';
}

function showToast(msg, type = '') {
  const toast = document.getElementById('toast');
  if (!toast) return;
  toast.textContent = msg;
  toast.classList.add('visible');
  setTimeout(() => toast.classList.remove('visible'), 2500);
}

function escapeHtml(str) {
  if (!str) return '';
  return str.replace(/[&<>]/g, function (m) {
    if (m === '&') return '&amp;';
    if (m === '<') return '&lt;';
    if (m === '>') return '&gt;';
    return m;
  });
}

// ---------- EVENT LISTENERS & INIT ----------
document.addEventListener('DOMContentLoaded', () => {
  // attach input listeners for preview
  const previewFields = ['fName', 'fPlatform', 'fCategory', 'fPrice', 'fOrigPrice', 'fDiscount', 'fRating', 'fSold', 'fEmoji'];
  previewFields.forEach((id) => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('input', updatePreview);
  });

  // close dropdown when clicking outside
  window.addEventListener('click', (e) => {
    const wrap = document.getElementById('userChipWrap');
    if (wrap && !wrap.contains(e.target)) wrap.classList.remove('open');
  });

  loadInitialData();
});