const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');

registerBtn.addEventListener('click', () => {
  container.classList.add("active");
});

loginBtn.addEventListener('click', () => {
  container.classList.remove("active");
});

// Dummy users (sebelum ada database)
const dummyUsers = [
  { email: "user@example.com", password: "password123", name: "User" },
  { email: "admin@test.com", password: "admin456", name: "Admin" },
  { email: "demo@demo.com", password: "demo123", name: "Demo" }
];

const loginForm = document.getElementById("loginForm");

loginForm.addEventListener("submit", function(e) {
  e.preventDefault();

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
    window.location.href = "search-after-login.html";
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