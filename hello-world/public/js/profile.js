// ── Init ──
document.addEventListener('DOMContentLoaded', () => {
  initProfile();
});

function initials(name) {
  return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
}

function initProfile() {
  const name  = SESSION_USER.nama;
  const email = SESSION_USER.email;

  // Nav chip
  document.getElementById('navAvatar').textContent = initials(name);
  document.getElementById('navName').textContent   = name;
  document.getElementById('dropName').textContent  = name;
  document.getElementById('dropEmail').textContent = email;

  // Hero
  document.getElementById('heroAvatar').textContent = initials(name);
  document.getElementById('heroName').textContent   = name;
  document.getElementById('heroEmail').textContent  = email;

  // Info panel
  document.getElementById('infoName').textContent  = name;
  document.getElementById('infoEmail').textContent = email;

  loadFavorites();
  loadHistory();
}

// ── Favorites ──
const mockFavorites = [
  { name: 'iPhone 15 128GB',      platform: 'shopee',    color: '#ee4d2d', price: 12749000, cheap: true  },
  { name: 'Samsung Galaxy S24',   platform: 'shopee',    color: '#ee4d2d', price: 8299000,  cheap: true  },
  { name: 'AirPods Pro 2nd Gen',  platform: 'tokopedia', color: '#42b549', price: 3499000,  cheap: false },
  { name: 'Nike Air Max 270',     platform: 'shopee',    color: '#ee4d2d', price: 1799000,  cheap: true  },
  { name: 'Laptop Asus Vivobook', platform: 'lazada',    color: '#0f146b', price: 9750000,  cheap: false },
];

function fmt(n) { return 'Rp ' + n.toLocaleString('id-ID'); }

function loadFavorites() {
  const list = document.getElementById('favList');
  document.getElementById('metaFav').textContent = mockFavorites.length;

  if (!mockFavorites.length) {
    list.innerHTML = `<div class="empty-state"><div class="empty-icon">🔖</div><div class="empty-text">Belum ada favorit. Cari produk dulu yuk!</div></div>`;
    return;
  }
  list.innerHTML = mockFavorites.map((f, i) => `
    <div class="fav-item">
      <div class="fav-platform-dot" style="background:${f.color}"></div>
      <div class="fav-name">${f.name}</div>
      ${f.cheap ? '<span class="fav-badge">Termurah</span>' : ''}
      <div class="fav-price ${f.cheap ? 'cheap' : ''}">${fmt(f.price)}</div>
      <span class="fav-remove" onclick="removeFav(${i}, event)" title="Hapus">✕</span>
    </div>
  `).join('');
}

function removeFav(i, e) {
  e.stopPropagation();
  mockFavorites.splice(i, 1);
  loadFavorites();
  showToast('Favorit dihapus');
}

// ── History ──
const mockHistory = [
  { q: 'iPhone 15',          time: '2 menit lalu'  },
  { q: 'Samsung Galaxy S24', time: '1 jam lalu'     },
  { q: 'AirPods Pro',        time: '3 jam lalu'     },
  { q: 'Nike Air Max',       time: 'Kemarin 14:32'  },
  { q: 'Laptop Asus',        time: 'Kemarin 10:15'  },
  { q: 'PS5',                time: '2 hari lalu'    },
  { q: 'Xiaomi Redmi Note',  time: '3 hari lalu'    },
];

function loadHistory() {
  const list = document.getElementById('historyList');
  document.getElementById('metaSearch').textContent = mockHistory.length;

  if (!mockHistory.length) {
    list.innerHTML = `<div class="empty-state"><div class="empty-icon">🔍</div><div class="empty-text">Riwayat pencarian kosong.</div></div>`;
    return;
  }
  list.innerHTML = mockHistory.map(h => `
    <div class="history-item" onclick="goSearch('${h.q}')">
      <div class="history-icon">🔍</div>
      <div class="history-query">${h.q}</div>
      <div class="history-time">${h.time}</div>
    </div>
  `).join('');
}

function goSearch(q) {
  window.location.href = BASE_URL + 'list?q=' + encodeURIComponent(q);
}

function clearHistory() {
  mockHistory.length = 0;
  loadHistory();
  showToast('Riwayat pencarian dihapus');
}

// ── Dropdown ──
function toggleDropdown() {
  document.getElementById('userChipWrap').classList.toggle('open');
}
document.addEventListener('click', (e) => {
  if (!document.getElementById('userChipWrap').contains(e.target)) {
    document.getElementById('userChipWrap').classList.remove('open');
  }
});

// ── Edit Modal ──
function openEdit() {
  document.getElementById('editModal').classList.add('open');
}
function closeEdit() {
  document.getElementById('editModal').classList.remove('open');
}
function saveEdit() {
  const name  = document.getElementById('editName').value.trim();
  const email = document.getElementById('editEmail').value.trim();
  if (!name || !email) { showToast('Nama dan email tidak boleh kosong'); return; }

  const params = new URLSearchParams({ nama_lengkap: name, email: email });

  fetch(BASE_URL + 'profile/update', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: params.toString()
  })
  .then(res => {
    if (res.ok || res.redirected) {
      SESSION_USER.nama  = name;
      SESSION_USER.email = email;
      closeEdit();
      initProfile();
      showToast('Profil berhasil diperbarui');
    } else {
      showToast('Gagal memperbarui profil');
    }
  })
  .catch(() => showToast('Terjadi kesalahan'));
}

// ── Settings ──
function saveSettings() { showToast('✓ Pengaturan disimpan'); }

// ── Logout ──
function doLogout() {
  window.location.href = BASE_URL + 'logout';
}

// ── Toast ──
function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2400);
}