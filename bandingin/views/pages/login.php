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
        <form id="loginForm" action="<?= BASE_URL . 'login' ?>" method="post">
          <input type="hidden" name="role" class="roleInput" value="user">
          <div class="icon-circle" id="loginIconCircle">
            <i class="fa-solid fa-user" id="loginIcon"></i>
          </div>
          <h2 id="loginTitle">MY ACCOUNT</h2>

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

          <button type="submit" class="primary-btn">SIGN IN</button>
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
</body>
</html>