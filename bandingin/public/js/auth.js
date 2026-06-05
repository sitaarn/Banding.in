const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');

registerBtn.addEventListener('click', () => {
  container.classList.add("active");
});

loginBtn.addEventListener('click', () => {
  container.classList.remove("active");
});

// Role Toggle Logic
const btnUser = document.getElementById('btnUser');
const btnSeller = document.getElementById('btnSeller');
const toggleBg = document.getElementById('toggleBg');
const roleInputs = document.querySelectorAll('.roleInput');

// Login Form Elements
const loginTitle = document.getElementById('loginTitle');
const loginIconCircle = document.getElementById('loginIconCircle');
const loginIcon = document.getElementById('loginIcon');
const usernameInput = document.getElementById('username');
const passwordInput = document.getElementById('password');

// Register Form Elements
const registerTitle = document.getElementById('registerTitle');
const regFullName = document.getElementById('regFullName');
const registerBtnText = document.getElementById('registerBtnText');
const regEmail = document.getElementById('regEmail');
const regUsername = document.getElementById('regUsername');
const regPassword = document.getElementById('regPassword');
const regConfirmPassword = document.getElementById('regConfirmPassword');

// Overlay Elements
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

function switchToSeller() {
    toggleBg.style.left = '50%';
    btnUser.classList.remove('active');
    btnSeller.classList.add('active');
    roleInputs.forEach(input => input.value = 'seller');

    // Update Login UI
    loginTitle.textContent = AUTH_LANG.login_title_seller;
    loginIcon.className = 'fa-solid fa-store';
    loginIconCircle.style.background = '#eceae4';
    usernameInput.placeholder = AUTH_LANG.ph_username_seller;
    passwordInput.placeholder = AUTH_LANG.ph_password_seller;

    // Update Register UI
    registerTitle.textContent = AUTH_LANG.register_title_seller;
    regUsername.placeholder = AUTH_LANG.ph_username_seller;
    regFullName.placeholder = AUTH_LANG.ph_store_name;
    regEmail.placeholder = AUTH_LANG.ph_business_email;
    regPassword.placeholder = AUTH_LANG.ph_password_seller;
    regConfirmPassword.placeholder = AUTH_LANG.ph_confirm_password;
    registerBtnText.textContent = 'REGISTER';

    // Update Overlay UI
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

function switchToUser() {
    toggleBg.style.left = '0';
    btnSeller.classList.remove('active');
    btnUser.classList.add('active');
    roleInputs.forEach(input => input.value = 'user');

    // Revert Login UI
    loginTitle.textContent = AUTH_LANG.login_title_user;
    loginIcon.className = 'fa-solid fa-user';
    loginIconCircle.style.background = '#eceae4';
    usernameInput.placeholder = AUTH_LANG.ph_username;
    passwordInput.placeholder = AUTH_LANG.ph_password;

    // Revert Register UI
    registerTitle.textContent = AUTH_LANG.register_title_user;
    regUsername.placeholder = AUTH_LANG.ph_username_reg;
    regFullName.placeholder = AUTH_LANG.ph_full_name;
    regEmail.placeholder = AUTH_LANG.ph_email;
    regPassword.placeholder = AUTH_LANG.ph_password_reg;
    regConfirmPassword.placeholder = AUTH_LANG.ph_confirm_password;
    registerBtnText.textContent = AUTH_LANG.sign_up;

    // Revert Overlay UI
    overlayRightTitle.textContent = AUTH_LANG.overlay_hello;
    overlayRightText.textContent = AUTH_LANG.overlay_enter_details;
    overlayRegisterBtn.textContent = 'Sign Up';

    overlayLeftTitle.textContent = AUTH_LANG.overlay_welcome;
    overlayLeftText.style.display = 'block';
    overlayLeftExtra.style.display = 'none';
}

btnSeller.addEventListener('click', switchToSeller);
btnUser.addEventListener('click', switchToUser);


// ══════════════════════════════════════════
//  PASSWORD TOGGLE VISIBILITY
// ══════════════════════════════════════════
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


// ══════════════════════════════════════════
//  FIELD VALIDATION
// ══════════════════════════════════════════

function validateField(input, errorSpanId, minLen, force = false) {
  const value = input.value.trim();
  const errorSpan = document.getElementById(errorSpanId);
  let errorMsg = '';

  // Required check
  if (!value) {
    if (force || input.classList.contains('input-error')) {
      errorMsg = AUTH_LANG.val_required;
    } else {
      input.classList.remove('input-error', 'input-valid');
      if (errorSpan) {
        errorSpan.textContent = '';
        errorSpan.classList.remove('visible');
      }
      return false; // Valid-ish state (no error shown) until forced
    }
  }
  // Min length check
  else if (minLen && value.length < minLen) {
    errorMsg = AUTH_LANG.val_min_chars.replace('%d', minLen);
  }
  // Email format check
  else if (input.type === 'email') {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(value)) {
      errorMsg = AUTH_LANG.val_email_invalid;
    }
  }
  // Confirm password check
  else if (input.name === 'confirm_password') {
    const pwField = document.getElementById('regPassword');
    if (pwField && value !== pwField.value) {
      errorMsg = AUTH_LANG.val_password_mismatch;
    }
  }

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

// ── Login form field validation (blur) ──
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

// ── Register form field validation (blur) ──
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

  el.addEventListener('blur', () => {
    validateField(el, errorId, min);
    // Special: check username uniqueness
    if (id === 'regUsername' && el.value.trim().length >= 3) {
      checkUsernameAvailability(el, errorId);
    }
  });

  el.addEventListener('input', () => {
    if (el.classList.contains('input-error')) {
      validateField(el, errorId, min);
    }
  });
});

// When password changes, re-validate confirm password if it has content
const regPasswordField = document.getElementById('regPassword');
const regConfirmField = document.getElementById('regConfirmPassword');
if (regPasswordField && regConfirmField) {
  regPasswordField.addEventListener('input', () => {
    if (regConfirmField.value.trim()) {
      validateField(regConfirmField, 'regConfirmPasswordError', 8);
    }
  });
}

// Register form submit validation
const registerForm = document.getElementById('registerForm');
if (registerForm) {
  registerForm.addEventListener('submit', function(e) {
    let hasError = false;
    regFields.forEach(({ id, errorId, min }) => {
      const el = document.getElementById(id);
      if (el && !validateField(el, errorId, min, true)) {
        hasError = true;
      }
    });
    if (hasError) {
      e.preventDefault();
    }
  });
}


// ══════════════════════════════════════════
//  USERNAME AVAILABILITY CHECK (AJAX)
// ══════════════════════════════════════════
let usernameCheckTimeout = null;

function checkUsernameAvailability(input, errorSpanId) {
  clearTimeout(usernameCheckTimeout);
  const username = input.value.trim();
  if (username.length < 3) return;

  usernameCheckTimeout = setTimeout(async () => {
    try {
      const res = await fetch(BASE_URL_AUTH + 'api/check-username?username=' + encodeURIComponent(username));
      const data = await res.json();
      
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
      // Silently fail - server-side will catch duplicates anyway
    }
  }, 500);
}