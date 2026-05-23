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
  const name     = document.getElementById('editName').value.trim();
  const email    = document.getElementById('editEmail').value.trim();
  const password = document.getElementById('editPass').value;

  if (!name || !email) { showToast('Nama dan email tidak boleh kosong'); return; }

  const params = new URLSearchParams({ nama_lengkap: name, email: email });

  // Tambahkan password hanya jika diisi
  if (password.length > 0) {
    // password baru butuh endpoint changePassword — handle terpisah
    // atau gabungkan di updateProfil jika backend mendukung
    params.append('new_password', password);
  }

  fetch(BASE_URL + 'profile/update', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: params.toString(),
    redirect: 'follow'   // ikuti redirect dari PHP
  })
  .then(res => {
    if (res.ok || res.redirected) {
      // Reload halaman agar PHP render ulang data dari DB/session
      window.location.reload();
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