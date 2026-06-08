/**
 * ============================================
 * profile.js - Logika Halaman Profil User
 * Banding.in - Perbandingan Harga E-commerce
 * ============================================
 * 
 * Menangani:
 * - Inisialisasi data profil (nama, email, avatar)
 * - Edit profil via modal (nama, email, password baru)
 * - Dropdown menu navigasi
 * - Toast notification
 * - Logout
 */

// ── Init: jalankan saat DOM ready ──
document.addEventListener('DOMContentLoaded', () => {
  initProfile();
});

/** Generate inisial dari nama (maks 2 huruf). Contoh: "John Doe" → "JD" */
function initials(name) {
  return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
}

/**
 * Inisialisasi profil: isi semua elemen UI dengan data user dari SESSION_USER.
 * SESSION_USER di-inject dari PHP ke JavaScript.
 */
function initProfile() {
  const name  = SESSION_USER.nama;
  const email = SESSION_USER.email;

  // Navbar - user chip
  document.getElementById('navAvatar').textContent = initials(name);
  document.getElementById('navName').textContent   = name;
  document.getElementById('dropName').textContent  = name;
  document.getElementById('dropEmail').textContent = email;

  // Hero section
  document.getElementById('heroAvatar').textContent = initials(name);
  document.getElementById('heroName').textContent   = name;
  document.getElementById('heroEmail').textContent  = email;

  // Info panel
  document.getElementById('infoName').textContent  = name;
  document.getElementById('infoEmail').textContent = email;

  // Load favorit dan riwayat pencarian
  loadFavorites();
  loadHistory();
}

/** Redirect ke halaman list dengan query pencarian */
function goSearch(q) {
  window.location.href = BASE_URL + 'list?q=' + encodeURIComponent(q);
}

/** Hapus riwayat pencarian */
function clearHistory() {
  mockHistory.length = 0;
  loadHistory();
  showToast('Riwayat pencarian dihapus');
}

// ── Dropdown User ──

/** Toggle buka/tutup dropdown user di navbar */
function toggleDropdown() {
  document.getElementById('userChipWrap').classList.toggle('open');
}

/** Tutup dropdown saat klik di luar area dropdown */
document.addEventListener('click', (e) => {
  if (!document.getElementById('userChipWrap').contains(e.target)) {
    document.getElementById('userChipWrap').classList.remove('open');
  }
});

// ── Edit Modal ──

/** Buka modal edit profil */
function openEdit() {
  document.getElementById('editModal').classList.add('open');
}

/** Tutup modal edit profil */
function closeEdit() {
  document.getElementById('editModal').classList.remove('open');
}

/**
 * Simpan perubahan profil.
 * Kirim data (nama, email, password baru) via POST ke backend.
 * Setelah berhasil, reload halaman agar data dari DB/session tampil.
 */
function saveEdit() {
  const name     = document.getElementById('editName').value.trim();
  const email    = document.getElementById('editEmail').value.trim();
  const password = document.getElementById('editPass').value;

  if (!name || !email) { showToast('Nama dan email tidak boleh kosong'); return; }

  const params = new URLSearchParams({ nama_lengkap: name, email: email });

  // Tambahkan password baru jika diisi
  if (password.length > 0) {
    params.append('new_password', password);
  }

  // Kirim ke endpoint update profil
  fetch(BASE_URL + 'profile/update', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: params.toString(),
    redirect: 'follow'   // Ikuti redirect dari PHP
  })
  .then(res => {
    if (res.ok || res.redirected) {
      window.location.reload(); // Reload agar data terbaru tampil
    } else {
      showToast('Gagal memperbarui profil');
    }
  })
  .catch(() => showToast('Terjadi kesalahan'));
}

// ── Settings ──
/** Simpan pengaturan (placeholder, belum ada implementasi nyata) */
function saveSettings() { showToast('✓ Pengaturan disimpan'); }

// ── Logout ──
/** Redirect ke endpoint logout */
function doLogout() {
  window.location.href = BASE_URL + 'logout';
}

// ── Toast Notification ──
/** Tampilkan toast notification selama 2.4 detik */
function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2400);
}