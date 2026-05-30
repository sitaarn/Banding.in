<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - <?= APP_NAME ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="./public/images/logo-b.png" type="image/png">
  <link rel="stylesheet" href="auth.css">
  <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/auth.css?v=<?= time() ?>">
</head>
<body>

  <div class="auth-wrapper">
    <div class="role-toggle-container">
      <div class="role-toggle">
        <div class="toggle-bg" id="toggleBg"></div>
        <button id="btnUser" class="toggle-btn active"><i class="fa-solid fa-user"></i> User</button>
        <button id="btnSeller" class="toggle-btn"><i class="fa-solid fa-store"></i> Seller</button>
      </div>
    </div>

    <div class="container" id="container">

      <!-- LOGIN FORM -->
      <div class="form-container login-container" >
        <form id="loginForm">
          <input type="hidden" name="role" class="roleInput" value="user">
          <div class="icon-circle" id="loginIconCircle">
            <i class="fa-solid fa-user" id="loginIcon"></i>
          </div>
          <h2 id="loginTitle">MY ACCOUNT</h2>

          <div id="loginError" style="display:none; background:rgba(224,82,82,0.15); border:1px solid rgba(224,82,82,0.3); color:#ff6b6b; padding:10px 14px; border-radius:10px; font-size:0.82rem; margin-bottom:12px; text-align:center;"></div>

          <input type="text"
          class="form-control"
          id="username"
          name="username"
          value="<?= e($_POST['username'] ?? '') ?>"
          placeholder="Enter username or email"
          required
          autofocus>
          <input type="password"
          class="form-control"
          id="password"
          name="password"
          placeholder="Enter password"
          required>

          <a href="<?= BASE_URL . 'forgot-password' ?>" style="font-size: 14px; margin-bottom: 15px; color: #666; text-decoration: none;">Forgot your password?</a>
          <button type="submit" class="primary-btn" id="loginSubmitBtn">SIGN IN</button>
        </form>
      </div>

      <!-- REGISTER FORM -->
      <div class="form-container register-container" style="overflow-y: auto;">
        <form action="<?= BASE_URL . 'register' ?>" method="post">
          <input type="hidden" name="role" class="roleInput" value="user">
          <h2 style="margin-top: 89px;" id="registerTitle">Create Account</h2>

          <input type="text" name="username" placeholder="Username" required>
          <input type="text" name="nama_lengkap" id="regFullName" placeholder="Full Name" required>
          <input type="email" name="email" id="regEmail" placeholder="Email" required>
          <input type="password" name="password" placeholder="Password" required>
          <input type="password" name="confirm_password" placeholder="Confirm Password" required>

          <button type="submit" class="primary-btn" id="registerBtnText">SIGN UP</button>
        </form>
      </div>

      <!-- OVERLAY -->
      <div class="overlay-container">
        <div class="overlay">

          <div class="overlay-panel left-panel">
            <h2 id="overlayLeftTitle">Welcome Back!</h2>
            <p id="overlayLeftText">To keep connected with us please login with your personal info</p>
            <div id="overlayLeftExtra" style="display: none; text-align: left; margin-bottom: 20px; background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px;">
                <h4 style="margin-bottom: 10px;">Why join Banding.in?</h4>
                <ul style="list-style: none; padding: 0; font-size: 14px; text-align: left;">
                    <li style="margin-bottom: 8px;"><i class="fa-solid fa-check-circle" style="color: #2ecad0;"></i> Competitively priced products will stand out on our platform</li>
                    <li style="margin-bottom: 8px;"><i class="fa-solid fa-check-circle" style="color: #2ecad0;"></i> Your store and products will be easily discoverable</li>
                    <li style="margin-bottom: 8px;"><i class="fa-solid fa-chart-line" style="color: #2ecad0;"></i> Access detailed analytics to track your product link clicks</li>
                </ul>
            </div>
            <button class="ghost" id="login">Sign In</button>
          </div>

          <div class="overlay-panel right-panel">
            <h2 id="overlayRightTitle">Hello, Friend!</h2>
            <p id="overlayRightText">Enter your personal details and start your journey</p>
            <button class="ghost" id="register">Sign Up</button>
          </div>

        </div>
      </div>

    </div>
  </div>

  <script src="<?= BASE_URL ?>public/js/auth.js?v=<?= time() ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <?php
    $flash = getFlashMessage();
    if ($flash) {
        $icon = $flash['type'] === 'success' ? 'success' : 'error';
        $title = $flash['type'] === 'success' ? 'Berhasil' : 'Gagal';
        echo "<script>
            Swal.fire({
                icon: '{$icon}',
                title: '{$title}',
                text: '" . addslashes($flash['message']) . "',
                background: '#1a2744',
                color: '#e8edf2',
                confirmButtonColor: '#2ecad0',
                customClass: { popup: 'swal-custom' },
                heightAuto: false
            });
        </script>";
    }
    if (isset($_SESSION['errors_messages']) && !empty($_SESSION['errors_messages'])) {
        $errs = $_SESSION['errors_messages'];
        $errStr = is_array($errs) ? implode("\\n", $errs) : $errs;
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '" . addslashes($errStr) . "',
                background: '#1a2744',
                color: '#e8edf2',
                confirmButtonColor: '#2ecad0',
                customClass: { popup: 'swal-custom' },
                heightAuto: false
            });
        </script>";
        unset($_SESSION['errors_messages']);
    }
  ?>

  <script>
  // AJAX Login - No page refresh on error
  const loginForm = document.getElementById('loginForm');
  const loginError = document.getElementById('loginError');
  const loginSubmitBtn = document.getElementById('loginSubmitBtn');

  loginForm.addEventListener('submit', async function(e) {
    e.preventDefault();

    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;
    const role = loginForm.querySelector('.roleInput').value;

    // Client-side validation
    if (!username) {
      showLoginError('Username wajib diisi.');
      return;
    }
    if (!password) {
      showLoginError('Password wajib diisi.');
      return;
    }

    // Show loading state
    loginSubmitBtn.disabled = true;
    loginSubmitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Signing in...';
    loginError.style.display = 'none';

    try {
      const formData = new FormData();
      formData.append('username', username);
      formData.append('password', password);
      formData.append('role', role);

      const response = await fetch('<?= BASE_URL ?>login', {
        method: 'POST',
        body: formData
      });

      // If response redirects (302), fetch follows it automatically
      // Check if the final URL is still the login page (error) or a different page (success)
      const responseUrl = response.url;
      const responseText = await response.text();

      // Check if we got redirected back to login page (contains login form)
      const isLoginPage = responseText.includes('id="loginForm"') || responseText.includes('id="loginTitle"');

      if (isLoginPage) {
        // Extract error from the response HTML
        const parser = new DOMParser();
        const doc = parser.parseFromString(responseText, 'text/html');
        
        // Look for SweetAlert script with error
        const scripts = doc.querySelectorAll('script');
        let errorMsg = 'Username atau password salah.';
        scripts.forEach(script => {
          const content = script.textContent;
          if (content.includes("Swal.fire") && content.includes("error")) {
            const textMatch = content.match(/text:\s*'([^']+)'/);
            if (textMatch) errorMsg = textMatch[1];
          }
        });

        showLoginError(errorMsg);
      } else {
        // Success - redirect to the final URL
        window.location.href = responseUrl;
      }
    } catch (error) {
      showLoginError('Terjadi kesalahan. Coba lagi.');
    } finally {
      loginSubmitBtn.disabled = false;
      loginSubmitBtn.innerHTML = 'SIGN IN';
    }
  });

  function showLoginError(msg) {
    loginError.textContent = msg;
    loginError.style.display = 'block';
    
    // Shake animation
    loginError.style.animation = 'none';
    loginError.offsetHeight; // trigger reflow
    loginError.style.animation = 'shakeError 0.4s ease';
    
    // Also show SweetAlert for prominence
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: msg,
      background: '#1a2744',
      color: '#e8edf2',
      confirmButtonColor: '#2ecad0',
      customClass: { popup: 'swal-custom' },
      heightAuto: false,
      timer: 3000,
      timerProgressBar: true
    });
  }
  </script>

  <style>
      .swal-custom {
          border: 1px solid rgba(255,255,255,0.1);
          box-shadow: 0 8px 32px rgba(0,0,0,0.5);
      }
      @keyframes shakeError {
        0%, 100% { transform: translateX(0); }
        20% { transform: translateX(-8px); }
        40% { transform: translateX(8px); }
        60% { transform: translateX(-5px); }
        80% { transform: translateX(5px); }
      }
  </style>
</body>
</html>