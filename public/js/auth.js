const container = document.getElementById('container');
const containerSeller = document.getElementById('containerSeller');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');
const sellerRegisterBtn = document.getElementById('sellerRegister');
const sellerLoginBtn = document.getElementById('sellerLogin');

const tabUser = document.getElementById('tabUser');
const tabSeller = document.getElementById('tabSeller');
const roleToggle = document.getElementById('roleToggle');

// User Login/Register Switch
if (registerBtn && loginBtn && container) {
  registerBtn.addEventListener('click', () => {
    container.classList.add("active");
  });

  loginBtn.addEventListener('click', () => {
    container.classList.remove("active");
  });
}

// Seller Login/Register Switch
if (sellerRegisterBtn && sellerLoginBtn && containerSeller) {
  sellerRegisterBtn.addEventListener('click', () => {
    containerSeller.classList.add("active");
  });

  sellerLoginBtn.addEventListener('click', () => {
    containerSeller.classList.remove("active");
  });
}

// Role Toggle Functionality
function setActiveRole(role) {
  // Reset slide state (remove active class) for both containers
  if (container) container.classList.remove('active');
  if (containerSeller) containerSeller.classList.remove('active');

  if (role === 'user') {
    tabUser.classList.add('active');
    tabSeller.classList.remove('active');
    roleToggle.classList.remove('seller-active');
    roleToggle.classList.add('user-active');
    
    if (container) {
      container.style.display = 'block';
      container.classList.remove('hidden-mode');
      container.classList.add('active-mode');
    }
    if (containerSeller) {
      containerSeller.style.display = 'none';
      containerSeller.classList.add('hidden-mode');
      containerSeller.classList.remove('active-mode');
    }
  } else {
    tabSeller.classList.add('active');
    tabUser.classList.remove('active');
    roleToggle.classList.remove('user-active');
    roleToggle.classList.add('seller-active');
    
    if (containerSeller) {
      containerSeller.style.display = 'block';
      containerSeller.classList.remove('hidden-mode');
      containerSeller.classList.add('active-mode');
    }
    if (container) {
      container.style.display = 'none';
      container.classList.add('hidden-mode');
      container.classList.remove('active-mode');
    }
  }
}

if (tabUser && tabSeller) {
  tabUser.addEventListener('click', () => {
    setActiveRole('user');
  });

  tabSeller.addEventListener('click', () => {
    setActiveRole('seller');
  });
  
  // Set initial state on load based on which tab PHP rendered as active
  if (tabSeller.classList.contains('active')) {
    setActiveRole('seller');
  } else {
    setActiveRole('user');
  }
}

// Dummy users (sebelum ada database)
const dummyUsers = [
  { email: "user@example.com", password: "password123", name: "User" },
  { email: "admin@test.com", password: "admin456", name: "Admin" },
  { email: "demo@demo.com", password: "demo123", name: "Demo" }
];

const loginForm = document.getElementById("loginForm");

loginForm.addEventListener("submit", function(e) {

  const emailInput = loginForm.querySelector('input[type="email"]');
  const passwordInput = loginForm.querySelector('input[type="password"]');

  const enteredEmail = emailInput.value.trim();
  const enteredPassword = passwordInput.value;

  const matched = dummyUsers.find(
    u => u.email === enteredEmail && u.password === enteredPassword
  );

  if (matched) {
    // Simpan session ke localStorage
    localStorage.setItem("loggedIn", "true");
    localStorage.setItem("userName", matched.name);
    localStorage.setItem("userEmail", matched.email);
    // Redirect ke halaman pencarian (search.html) setelah login
    window.location.href = "search.html";
  } else {
    let errorMsg = loginForm.querySelector('.error-msg');
    if (!errorMsg) {
      errorMsg = document.createElement('p');
      errorMsg.className = 'error-msg';
      errorMsg.style.cssText = 'color:red;font-size:13px;margin-top:10px;';
      loginForm.appendChild(errorMsg);
    }
    errorMsg.textContent = 'Email atau password salah. Coba lagi.';
  }
});