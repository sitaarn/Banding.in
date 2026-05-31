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

// Overlay Elements
const overlayLeftTitle = document.getElementById('overlayLeftTitle');
const overlayLeftText = document.getElementById('overlayLeftText');
const overlayLeftExtra = document.getElementById('overlayLeftExtra');
const overlayRightTitle = document.getElementById('overlayRightTitle');
const overlayRightText = document.getElementById('overlayRightText');
const overlayRegisterBtn = document.getElementById('register');

function switchToSeller() {
    // Animate toggle background
    toggleBg.style.left = '50%';
    
    btnUser.classList.remove('active');
    btnSeller.classList.add('active');
    
    roleInputs.forEach(input => input.value = 'seller');
    
    // Update Login UI
    loginTitle.textContent = 'LOGIN SELLER';
    loginIcon.className = 'fa-solid fa-store';
    loginIconCircle.style.background = '#eceae4';
    usernameInput.placeholder = 'Masukkan username atau email seller';
    passwordInput.placeholder = 'Password seller';
    
    // Update Register UI
    registerTitle.textContent = 'Daftar';
    regFullName.placeholder = 'Nama Toko';
    regEmail.placeholder = 'Email Bisnis';
    registerBtnText.textContent = 'REGISTER';
    
    // Update Overlay UI
    overlayRightTitle.textContent = 'Mulai Berjualan!';
    overlayRightText.textContent = 'Daftarkan toko Anda dan jangkau jutaan pembeli';
    overlayRegisterBtn.textContent = 'Register';
    
    overlayLeftTitle.textContent = 'Mengapa bergabung dengan Banding.in?';
    overlayLeftText.style.display = 'none';
    overlayLeftExtra.style.display = 'block';
}

function switchToUser() {
    // Animate toggle background
    toggleBg.style.left = '0';
    
    btnSeller.classList.remove('active');
    btnUser.classList.add('active');
    
    roleInputs.forEach(input => input.value = 'user');
    
    // Revert Login UI
    loginTitle.textContent = 'AKUN SAYA';
    loginIcon.className = 'fa-solid fa-user';
    loginIconCircle.style.background = '#eceae4';
    usernameInput.placeholder = 'Masukkan username atau email';
    passwordInput.placeholder = 'Masukkan password';
    
    // Revert Register UI
    registerTitle.textContent = 'Buat Akun';
    regFullName.placeholder = 'Nama Lengkap';
    regEmail.placeholder = 'Email';
    registerBtnText.textContent = 'SIGN UP';
    
    // Revert Overlay UI
    overlayRightTitle.textContent = 'Halo, Teman!';
    overlayRightText.textContent = 'Masukkan detail pribadi Anda dan mulai perjalanan Anda';
    overlayRegisterBtn.textContent = 'Sign Up';
    
    overlayLeftTitle.textContent = 'Selamat Datang Kembali!';
    overlayLeftText.style.display = 'block';
    overlayLeftExtra.style.display = 'none';
}

btnSeller.addEventListener('click', switchToSeller);
btnUser.addEventListener('click', switchToUser);