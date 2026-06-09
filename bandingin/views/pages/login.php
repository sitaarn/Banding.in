<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - <?= APP_NAME ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="./public/images/favicon.png" type="image/png">
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
        <form id="loginForm" novalidate>
          <input type="hidden" name="role" class="roleInput" value="user">
          <div class="icon-circle" id="loginIconCircle">
            <i class="fa-solid fa-user" id="loginIcon"></i>
          </div>
          <h2 id="loginTitle"><?= __('login_title_user') ?></h2>

          <div id="loginError" style="display:none; background:rgba(224,82,82,0.15); border:1px solid rgba(224,82,82,0.3); color:#ff6b6b; padding:10px 14px; border-radius:10px; font-size:0.82rem; margin-bottom:12px; text-align:center;"></div>

          <input type="text"
          class="form-control"
          id="username"
          name="username"
          value="<?= e($_POST['username'] ?? '') ?>"
          placeholder="<?= __('ph_username') ?>"
          required
          autofocus>
          <span class="field-error" id="loginUsernameError"></span>

          <div class="input-wrapper">
            <input type="password"
            class="form-control"
            id="password"
            name="password"
            placeholder="<?= __('ph_password') ?>"
            required>
            <button type="button" class="toggle-password" onclick="togglePasswordVisibility('password', this)">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
          <span class="field-error" id="loginPasswordError"></span>

          <!-- CHECKBOX REMEMBER ME: Dicentang jika user ingin tetap login secara otomatis di perangkat ini -->
          <div class="remember-me-container">
            <label class="remember-me-label">
              <input type="checkbox" id="remember" name="remember" value="1">
              <span><?= __('remember_me') ?></span>
            </label>
          </div>

          <button type="submit" class="primary-btn" id="loginSubmitBtn" style="margin-top: 15px;"><?= __('sign_in') ?></button>
        </form>
      </div>

      <!-- REGISTER FORM -->
      <div class="form-container register-container" style="overflow-y: auto;">
        <form action="<?= BASE_URL . 'register' ?>" method="post" id="registerForm" novalidate>
          <input type="hidden" name="role" class="roleInput" value="user">
          <h2 style="margin-top: 89px;" id="registerTitle"><?= __('register_title_user') ?></h2>

          <input type="text" name="username" id="regUsername" placeholder="<?= __('ph_username_reg') ?>" required minlength="3">
          <span class="field-error" id="regUsernameError"></span>

          <input type="text" name="nama_lengkap" id="regFullName" placeholder="<?= __('ph_full_name') ?>" required minlength="3">
          <span class="field-error" id="regFullNameError"></span>

          <input type="email" name="email" id="regEmail" placeholder="<?= __('ph_email') ?>" required>
          <span class="field-error" id="regEmailError"></span>

          <div class="input-wrapper">
            <input type="password" name="password" id="regPassword" placeholder="<?= __('ph_password_reg') ?>" required minlength="8">
            <button type="button" class="toggle-password" onclick="togglePasswordVisibility('regPassword', this)">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
          <span class="field-error" id="regPasswordError"></span>

          <div class="input-wrapper">
            <input type="password" name="confirm_password" id="regConfirmPassword" placeholder="<?= __('ph_confirm_password') ?>" required minlength="8">
            <button type="button" class="toggle-password" onclick="togglePasswordVisibility('regConfirmPassword', this)">
              <i class="fa-solid fa-eye"></i>
            </button>
          </div>
          <span class="field-error" id="regConfirmPasswordError"></span>

          <button type="submit" class="primary-btn" id="registerBtnText"><?= __('sign_up') ?></button>
        </form>
      </div>

      <!-- OVERLAY -->
      <div class="overlay-container">
        <div class="overlay">

          <div class="overlay-panel left-panel">
            <h2 id="overlayLeftTitle"><?= __('overlay_welcome') ?></h2>
            <p id="overlayLeftText"><?= __('overlay_stay_connected') ?></p>
            <div id="overlayLeftExtra" style="display: none; text-align: left; margin-bottom: 20px; background: rgba(255,255,255,0.1); padding: 15px; border-radius: 10px;">
                <h4 style="margin-bottom: 10px;" id="overlayWhyJoinTitle"><?= __('overlay_why_join') ?></h4>
                <ul style="list-style: none; padding: 0; font-size: 14px; text-align: left;">
                    <li style="margin-bottom: 8px;"><i class="fa-solid fa-check-circle" style="color: #2ecad0;"></i> <span id="overlayBenefit1"><?= __('overlay_benefit_1') ?></span></li>
                    <li style="margin-bottom: 8px;"><i class="fa-solid fa-check-circle" style="color: #2ecad0;"></i> <span id="overlayBenefit2"><?= __('overlay_benefit_2') ?></span></li>
                    <li style="margin-bottom: 8px;"><i class="fa-solid fa-chart-line" style="color: #2ecad0;"></i> <span id="overlayBenefit3"><?= __('overlay_benefit_3') ?></span></li>
                </ul>
            </div>
            <button class="ghost" id="login">Sign In</button>
          </div>

          <div class="overlay-panel right-panel">
            <h2 id="overlayRightTitle"><?= __('overlay_hello') ?></h2>
            <p id="overlayRightText"><?= __('overlay_enter_details') ?></p>
            <button class="ghost" id="register">Sign Up</button>
          </div>

        </div>
      </div>

    </div>
  </div>

  <script>
    // Translation strings for JS
    const AUTH_LANG = {
      login_title_user: "<?= __('login_title_user') ?>",
      login_title_seller: "<?= __('login_title_seller') ?>",
      register_title_user: "<?= __('register_title_user') ?>",
      register_title_seller: "<?= __('register_title_seller') ?>",
      ph_username: "<?= __('ph_username') ?>",
      ph_password: "<?= __('ph_password') ?>",
      ph_username_seller: "<?= __('ph_username_seller') ?>",
      ph_password_seller: "<?= __('ph_password_seller') ?>",
      ph_store_name: "<?= __('ph_store_name') ?>",
      ph_business_email: "<?= __('ph_business_email') ?>",
      ph_username_reg: "<?= __('ph_username_reg') ?>",
      ph_full_name: "<?= __('ph_full_name') ?>",
      ph_email: "<?= __('ph_email') ?>",
      ph_password_reg: "<?= __('ph_password_reg') ?>",
      ph_confirm_password: "<?= __('ph_confirm_password') ?>",
      overlay_welcome: "<?= __('overlay_welcome') ?>",
      overlay_stay_connected: "<?= __('overlay_stay_connected') ?>",
      overlay_hello: "<?= __('overlay_hello') ?>",
      overlay_enter_details: "<?= __('overlay_enter_details') ?>",
      overlay_start_selling: "<?= __('overlay_start_selling') ?>",
      overlay_register_store: "<?= __('overlay_register_store') ?>",
      overlay_why_join: "<?= __('overlay_why_join') ?>",
      overlay_benefit_1: "<?= __('overlay_benefit_1') ?>",
      overlay_benefit_2: "<?= __('overlay_benefit_2') ?>",
      overlay_benefit_3: "<?= __('overlay_benefit_3') ?>",
      sign_up: "<?= __('sign_up') ?>",
      sign_in: "<?= __('sign_in') ?>",
      signing_in: "<?= __('signing_in') ?>",
      val_required: "<?= __('val_required') ?>",
      val_min_chars: "<?= __('val_min_chars') ?>",
      val_email_invalid: "<?= __('val_email_invalid') ?>",
      val_password_mismatch: "<?= __('val_password_mismatch') ?>",
      val_username_taken: "<?= __('val_username_taken') ?>",
      val_username_required: "<?= __('val_username_required') ?>",
      val_password_required: "<?= __('val_password_required') ?>",
      val_login_failed: "<?= __('val_login_failed') ?>",
      val_error_occurred: "<?= __('val_error_occurred') ?>",
    };
    const BASE_URL_AUTH = "<?= BASE_URL ?>";
  </script>
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
                background: '#ffffff',
                color: '#1c1c1c',
                confirmButtonColor: '#1c1c1c',
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
                background: '#ffffff',
                color: '#1c1c1c',
                confirmButtonColor: '#1c1c1c',
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

    // Run validation first
    const usernameField = document.getElementById('username');
    const passwordField = document.getElementById('password');
    let hasError = false;

    if (!validateField(usernameField, 'loginUsernameError', null, true)) hasError = true;
    if (!validateField(passwordField, 'loginPasswordError', null, true)) hasError = true;

    if (hasError) return;

    const username = usernameField.value.trim();
    const password = passwordField.value;
    const role = loginForm.querySelector('.roleInput').value;

    // Show loading state
    loginSubmitBtn.disabled = true;
    loginSubmitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ' + AUTH_LANG.signing_in;
    loginError.style.display = 'none';

    try {
      // Ambil elemen checkbox "Remember Me" berdasarkan id
      const rememberCheckbox = document.getElementById('remember');
      
      const formData = new FormData();
      formData.append('username', username);
      formData.append('password', password);
      formData.append('role', role);
      
      // Jika checkbox dicentang, kirim parameter 'remember' bernilai '1' ke backend controller
      if (rememberCheckbox && rememberCheckbox.checked) {
        formData.append('remember', '1');
      }

      const response = await fetch('<?= BASE_URL ?>login', {
        method: 'POST',
        body: formData
      });

      const responseUrl = response.url;
      const responseText = await response.text();
      const isLoginPage = responseText.includes('id="loginForm"') || responseText.includes('id="loginTitle"');

      if (isLoginPage) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(responseText, 'text/html');
        const scripts = doc.querySelectorAll('script');
        let errorMsg = AUTH_LANG.val_login_failed;
        scripts.forEach(script => {
          const content = script.textContent;
          if (content.includes("Swal.fire") && content.includes("error")) {
            const textMatch = content.match(/text:\s*'([^']+)'/);
            if (textMatch) errorMsg = textMatch[1];
          }
        });
        showLoginError(errorMsg);
      } else {
        window.location.href = responseUrl;
      }
    } catch (error) {
      showLoginError(AUTH_LANG.val_error_occurred);
    } finally {
      loginSubmitBtn.disabled = false;
      loginSubmitBtn.innerHTML = AUTH_LANG.sign_in;
    }
  });

  function showLoginError(msg) {
    loginError.textContent = msg;
    loginError.style.display = 'block';
    
    loginError.style.animation = 'none';
    loginError.offsetHeight;
    loginError.style.animation = 'shakeError 0.4s ease';
    
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: msg,
      background: '#ffffff',
      color: '#1c1c1c',
      confirmButtonColor: '#1c1c1c',
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