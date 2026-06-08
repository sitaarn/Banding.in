/**
 * ============================================
 * scriptlanding.js - Logika Halaman Landing Page
 * Banding.in - Perbandingan Harga E-commerce
 * ============================================
 * 
 * Menangani:
 * - Navigasi ke halaman pencarian & login
 * - User dropdown menu (open/close)
 * - Toggle filter platform di landing page
 * - Inisial avatar user dari nama
 */

/** Redirect ke halaman pencarian produk */
function goToSearch() {
  window.location.href = '/bandingin/list';
}

/** Redirect ke halaman login, simpan URL asal agar bisa redirect balik setelah login */
function goToLogin() {
  localStorage.setItem('redirectAfterLogin', window.location.href);
  window.location.href = '/bandingin/login';
}

/** Tutup dropdown user saat klik di luar area dropdown */
window.addEventListener('click', function(e) {
  const wrap = document.getElementById('userChipWrap');
  if (wrap && !wrap.contains(e.target)) {
    wrap.classList.remove('open');
  }
});

/** Aktifkan klik pada label platform di landing (toggle active state) */
document.addEventListener('DOMContentLoaded', () => {
  const pfLabels = document.querySelectorAll('.landing-pf-label');
  pfLabels.forEach(label => {
    label.addEventListener('click', () => {
      pfLabels.forEach(l => l.classList.remove('active'));
      label.classList.add('active');
    });
  });
});

/** Toggle buka/tutup dropdown user di navbar */
function toggleDropdown() {
  const wrap = document.getElementById('userChipWrap');
  if (wrap) wrap.classList.toggle('open');
}

/** Proses logout: hapus data localStorage dan redirect ke endpoint logout */
function doLogout() {
  localStorage.removeItem('loggedIn');
  localStorage.removeItem('userName');
  localStorage.removeItem('userEmail');
  window.location.href = '/bandingin/logout';
}

/** Generate inisial dari nama (maks 2 huruf). Contoh: "John Doe" → "JD" */
function getInitials(name) {
  return name.split(' ').slice(0, 2).map(w => w[0]?.toUpperCase() || '').join('');
}

/** Saat DOM ready: set inisial avatar user dari atribut data-avatar */
document.addEventListener("DOMContentLoaded", function () {
  const avatarEl = document.getElementById('userAvatar');
  if (avatarEl) {
    let name = avatarEl.getAttribute('data-avatar');
    if (name) {
      avatarEl.innerHTML = getInitials(name);
    }
  }
});