/**
 * ============================================
 * auth.js - Logika Halaman Login & Register
 * Banding.in - Perbandingan Harga E-commerce
 * ============================================
 * 
 * Menangani:
 * - Toggle antara form Login dan Register (animasi slide)
 * - Switch mode User vs Seller (update UI login & register)
 * - Toggle visibility password (show/hide)
 * - Validasi field form secara real-time (blur & input)
 * - Cek ketersediaan username via AJAX
 */

// ═══════════════════════════════════════════
//  TOGGLE LOGIN ↔ REGISTER (Animasi Slide)
// ═══════════════════════════════════════════

const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');

// Klik "Register" → geser container ke kanan (tampilkan form register)
registerBtn.addEventListener('click', () => {
  container.classList.add("active");
});

// Klik "Login" → geser container ke kiri (tampilkan form login)
loginBtn.addEventListener('click', () => {
  container.classList.remove("active");
});

// ═══════════════════════════════════════════
//  SWITCH MODE: USER vs SELLER
// ═══════════════════════════════════════════

// Elemen toggle role (tombol User dan Seller)
const btnUser = document.getElementById('btnUser');
const btnSeller = document.getElementById('btnSeller');
const toggleBg = document.getElementById('toggleBg');
const roleInputs = document.querySelectorAll('.roleInput'); // Hidden input role di form

// Elemen form Login
const loginTitle = document.getElementById('loginTitle');
const loginIconCircle = document.getElementById('loginIconCircle');
const loginIcon = document.getElementById('loginIcon');
const usernameInput = document.getElementById('username');
const passwordInput = document.getElementById('password');

// Elemen form Register
const registerTitle = document.getElementById('registerTitle');
const regFullName = document.getElementById('regFullName');
const registerBtnText = document.getElementById('registerBtnText');
const regEmail = document.getElementById('regEmail');
const regUsername = document.getElementById('regUsername');
const regPassword = document.getElementById('regPassword');
const regConfirmPassword = document.getElementById('regConfirmPassword');

// Elemen Overlay (panel samping kiri/kanan)
const overlayLeftTitle = document.getElementById('overlayLeftTitle');
const overlayLeftText = document.getElementById('overlayLeftText');
const overlayLeftExtra = document.getElementById('overlayLeftExtra');
const overlayRightTitle = document.getElementById('overlayRightTitle');
const overlayRightText = document.getElementById('overlayRightText');
const overlayRegisterBtn = document.getElementById('register');
const overlayWhyJoinTitle = document.getElementById('overlayWhyJoinTitle');
const overlayBenefit1 = document.getElementById('overlayBenefit1');
const overlayBenefit2 = document.getElementById('overlayBenefit2');
const overlayBenefit3 = document.getElementById('overlayBenefit3');

/**
 * Switch ke mode Seller: update semua teks, ikon, dan placeholder
 * agar sesuai dengan konteks seller/penjual.
 */
function switchToSeller() {
    toggleBg.style.left = '50%';
    btnUser.classList.remove('active');
    btnSeller.classList.add('active');
    roleInputs.forEach(input => input.value = 'seller');

    // Update UI Login
    loginTitle.textContent = AUTH_LANG.login_title_seller;
    loginIcon.className = 'fa-solid fa-store';
    loginIconCircle.style.background = '#eceae4';
    usernameInput.placeholder = AUTH_LANG.ph_username_seller;
    passwordInput.placeholder = AUTH_LANG.ph_password_seller;

    // Update UI Register
    registerTitle.textContent = AUTH_LANG.register_title_seller;
    regUsername.placeholder = AUTH_LANG.ph_username_seller;
    regFullName.placeholder = AUTH_LANG.ph_store_name;
    regEmail.placeholder = AUTH_LANG.ph_business_email;
    regPassword.placeholder = AUTH_LANG.ph_password_seller;
    regConfirmPassword.placeholder = AUTH_LANG.ph_confirm_password;
    registerBtnText.textContent = 'REGISTER';

    // Update Overlay
    overlayRightTitle.textContent = AUTH_LANG.overlay_start_selling;
    overlayRightText.textContent = AUTH_LANG.overlay_register_store;
    overlayRegisterBtn.textContent = 'Register';
    overlayLeftTitle.textContent = AUTH_LANG.overlay_why_join;
    overlayLeftText.style.display = 'none';
    overlayLeftExtra.style.display = 'block';

    if (overlayWhyJoinTitle) overlayWhyJoinTitle.textContent = AUTH_LANG.overlay_why_join;
    if (overlayBenefit1) overlayBenefit1.textContent = AUTH_LANG.overlay_benefit_1;
    if (overlayBenefit2) overlayBenefit2.textContent = AUTH_LANG.overlay_benefit_2;
    if (overlayBenefit3) overlayBenefit3.textContent = AUTH_LANG.overlay_benefit_3;
}

/**
 * Switch ke mode User: kembalikan semua teks, ikon, placeholder
 * ke default untuk user biasa/pembeli.
 */
function switchToUser() {
    toggleBg.style.left = '0';
    btnSeller.classList.remove('active');
    btnUser.classList.add('active');
    roleInputs.forEach(input => input.value = 'user');

    // Revert UI Login
    loginTitle.textContent = AUTH_LANG.login_title_user;
    loginIcon.className = 'fa-solid fa-user';
    loginIconCircle.style.background = '#eceae4';
    usernameInput.placeholder = AUTH_LANG.ph_username;
    passwordInput.placeholder = AUTH_LANG.ph_password;

    // Revert UI Register
    registerTitle.textContent = AUTH_LANG.register_title_user;
    regUsername.placeholder = AUTH_LANG.ph_username_reg;
    regFullName.placeholder = AUTH_LANG.ph_full_name;
    regEmail.placeholder = AUTH_LANG.ph_email;
    regPassword.placeholder = AUTH_LANG.ph_password_reg;
    regConfirmPassword.placeholder = AUTH_LANG.ph_confirm_password;
    registerBtnText.textContent = AUTH_LANG.sign_up;

    // Revert Overlay
    overlayRightTitle.textContent = AUTH_LANG.overlay_hello;
    overlayRightText.textContent = AUTH_LANG.overlay_enter_details;
    overlayRegisterBtn.textContent = 'Sign Up';
    overlayLeftTitle.textContent = AUTH_LANG.overlay_welcome;
    overlayLeftText.style.display = 'block';
    overlayLeftExtra.style.display = 'none';
}

// Event listener: klik tombol Seller / User
btnSeller.addEventListener('click', switchToSeller);
btnUser.addEventListener('click', switchToUser);


// ═══════════════════════════════════════════
//  TOGGLE VISIBILITY PASSWORD (show/hide)
// ═══════════════════════════════════════════

/** Toggle tipe input antara 'password' dan 'text', ubah ikon mata */
function togglePasswordVisibility(inputId, btn) {
  const input = document.getElementById(inputId);
  const icon = btn.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.className = 'fa-solid fa-eye-slash';
  } else {
    input.type = 'password';
    icon.className = 'fa-solid fa-eye';
  }
}


// ═══════════════════════════════════════════
//  VALIDASI FIELD FORM (real-time)
// ═══════════════════════════════════════════

/**
 * Validasi satu field input. Cek required, minLength, email, confirm password.
 * Tampilkan error di span terkait. Return true jika valid.
 * 
 * @param {HTMLElement} input - Elemen input yang divalidasi
 * @param {string} errorSpanId - ID elemen span untuk tampilkan pesan error
 * @param {number|null} minLen - Panjang minimum (null = skip)
 * @param {boolean} force - Jika true, paksa tampilkan error meski belum pernah blur
 */
function validateField(input, errorSpanId, minLen, force = false) {
  const value = input.value.trim();
  const errorSpan = document.getElementById(errorSpanId);
  let errorMsg = '';

  // Cek: field wajib diisi
  if (!value) {
    if (force || input.classList.contains('input-error')) {
      errorMsg = AUTH_LANG.val_required;
    } else {
      input.classList.remove('input-error', 'input-valid');
      if (errorSpan) {
        errorSpan.textContent = '';
        errorSpan.classList.remove('visible');
      }
      return false;
    }
  }
  // Cek: panjang minimum
  else if (minLen && value.length < minLen) {
    errorMsg = AUTH_LANG.val_min_chars.replace('%d', minLen);
  }
  // Cek: format email
  else if (input.type === 'email') {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(value)) {
      errorMsg = AUTH_LANG.val_email_invalid;
    }
  }
  // Cek: confirm password harus cocok dengan password
  else if (input.name === 'confirm_password') {
    const pwField = document.getElementById('regPassword');
    if (pwField && value !== pwField.value) {
      errorMsg = AUTH_LANG.val_password_mismatch;
    }
  }

  // Tampilkan atau hapus error di UI
  if (errorMsg) {
    input.classList.add('input-error');
    input.classList.remove('input-valid');
    if (errorSpan) {
      errorSpan.textContent = errorMsg;
      errorSpan.classList.add('visible');
    }
    return false;
  } else {
    input.classList.remove('input-error');
    if (value) input.classList.add('input-valid');
    if (errorSpan) {
      errorSpan.textContent = '';
      errorSpan.classList.remove('visible');
    }
    return true;
  }
}

// ── Validasi field Login (saat blur) ──
const loginUsernameField = document.getElementById('username');
const loginPasswordField = document.getElementById('password');

if (loginUsernameField) {
  loginUsernameField.addEventListener('blur', () => {
    validateField(loginUsernameField, 'loginUsernameError', null);
  });
  loginUsernameField.addEventListener('input', () => {
    if (loginUsernameField.classList.contains('input-error')) {
      validateField(loginUsernameField, 'loginUsernameError', null);
    }
  });
}

if (loginPasswordField) {
  loginPasswordField.addEventListener('blur', () => {
    validateField(loginPasswordField, 'loginPasswordError', null);
  });
  loginPasswordField.addEventListener('input', () => {
    if (loginPasswordField.classList.contains('input-error')) {
      validateField(loginPasswordField, 'loginPasswordError', null);
    }
  });
}

// ── Validasi field Register (saat blur & input) ──
const regFields = [
  { id: 'regUsername', errorId: 'regUsernameError', min: 3 },
  { id: 'regFullName', errorId: 'regFullNameError', min: 3 },
  { id: 'regEmail', errorId: 'regEmailError', min: 0 },
  { id: 'regPassword', errorId: 'regPasswordError', min: 8 },
  { id: 'regConfirmPassword', errorId: 'regConfirmPasswordError', min: 8 },
];

regFields.forEach(({ id, errorId, min }) => {
  const el = document.getElementById(id);
  if (!el) return;

  // Validasi saat blur (keluar dari field)
  el.addEventListener('blur', () => {
    validateField(el, errorId, min);
    // Khusus username: cek ketersediaan via AJAX
    if (id === 'regUsername' && el.value.trim().length >= 3) {
      checkUsernameAvailability(el, errorId);
    }
  });

  // Re-validasi saat mengetik jika sedang error
  el.addEventListener('input', () => {
    if (el.classList.contains('input-error')) {
      validateField(el, errorId, min);
    }
  });
});

// Saat password berubah, re-validasi confirm password (jika sudah diisi)
const regPasswordField = document.getElementById('regPassword');
const regConfirmField = document.getElementById('regConfirmPassword');
if (regPasswordField && regConfirmField) {
  regPasswordField.addEventListener('input', () => {
    if (regConfirmField.value.trim()) {
      validateField(regConfirmField, 'regConfirmPasswordError', 8);
    }
  });
}

// ── Validasi saat submit form Register ──
const registerForm = document.getElementById('registerForm');
if (registerForm) {
  registerForm.addEventListener('submit', function(e) {
    let hasError = false;
    // Force validasi semua field saat submit
    regFields.forEach(({ id, errorId, min }) => {
      const el = document.getElementById(id);
      if (el && !validateField(el, errorId, min, true)) {
        hasError = true;
      }
    });
    if (hasError) {
      e.preventDefault(); // Cegah submit jika ada error
    }
  });
}

// ── Navigasi Input dengan tombol Enter ──

// Untuk form Register
const regInputOrder = ['regUsername', 'regFullName', 'regEmail', 'regPassword', 'regConfirmPassword'];
regInputOrder.forEach((id, index) => {
  const el = document.getElementById(id);
  if (!el) return;

  el.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      // Jika bukan input terakhir, pindah ke input berikutnya
      if (index < regInputOrder.length - 1) {
        e.preventDefault(); // Mencegah form ter-submit otomatis
        const nextEl = document.getElementById(regInputOrder[index + 1]);
        if (nextEl) {
          nextEl.focus();
        }
      }
      // Jika input terakhir (regConfirmPassword), biarkan submit default berjalan
    }
  });
});

// Untuk form Login
const loginInputOrder = ['username', 'password'];
loginInputOrder.forEach((id, index) => {
  const el = document.getElementById(id);
  if (!el) return;

  el.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      // Jika bukan input terakhir (username), pindah ke password
      if (index < loginInputOrder.length - 1) {
        e.preventDefault(); // Mencegah login form ter-submit otomatis
        const nextEl = document.getElementById(loginInputOrder[index + 1]);
        if (nextEl) {
          nextEl.focus();
        }
      }
    }
  });
});



// ═══════════════════════════════════════════
//  CEK KETERSEDIAAN USERNAME (AJAX)
// ═══════════════════════════════════════════

let usernameCheckTimeout = null; // Untuk debounce

/**
 * Cek apakah username sudah dipakai user lain via API.
 * Menggunakan debounce 500ms agar tidak terlalu sering request.
 */
function checkUsernameAvailability(input, errorSpanId) {
  clearTimeout(usernameCheckTimeout);
  const username = input.value.trim();
  if (username.length < 3) return;

  usernameCheckTimeout = setTimeout(async () => {
    try {
      const res = await fetch(BASE_URL_AUTH + 'api/check-username?username=' + encodeURIComponent(username));
      const data = await res.json();
      
      // Jika username sudah ada → tampilkan error
      if (data && data.exists) {
        const errorSpan = document.getElementById(errorSpanId);
        input.classList.add('input-error');
        input.classList.remove('input-valid');
        if (errorSpan) {
          errorSpan.textContent = AUTH_LANG.val_username_taken;
          errorSpan.classList.add('visible');
        }
      }
    } catch (err) {
      // Gagal cek → tidak apa-apa, server akan validasi saat submit
    }
  }, 500);
}