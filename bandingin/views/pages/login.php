<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - <?= APP_NAME ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="auth.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="./public/css/auth.css">
</head>
<body>

  <div class="container" id="container">

    <!-- LOGIN FORM -->
    <div class="form-container login-container" >
      <form id="loginForm" action="<?= BASE_URL . 'login' ?>" method="post">
        <div class="icon-circle">
          <i class="fa-solid fa-user"></i>
        </div>
        <h2>MY ACCOUNT</h2>

        <input type="text"
        class="form-control"
        id="username"
        name="username"
        value="<?= e($_POST['username'] ?? '') ?>"
        placeholder="Masukkan username atau email"
        required
        autofocus>
        <input type="password"
        class="form-control"
        id="password"
        name="password"
        placeholder="Masukkan password"
        required>

        <button type="submit" class="primary-btn">SIGN IN</button>
      </form>
    </div>

    <!-- REGISTER FORM -->
    <div class="form-container register-container" style="overflow-y: auto;">
      <form action="<?= BASE_URL . 'register' ?>" method="post">
        <h2 style="margin-top: 89px;">Create Account</h2>

        <input type="text" name="username" placeholder="Username" required>
        <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>


        <button type="submit" class="primary-btn">SIGN UP</button>

      
      </form>
    </div>

    <!-- OVERLAY -->
    <div class="overlay-container">
      <div class="overlay">

        <div class="overlay-panel left-panel">
          <h2>Welcome Back!</h2>
          <p>To keep connected with us please login with your personal info</p>
          <button class="ghost" id="login">Sign In</button>
        </div>

        <div class="overlay-panel right-panel">
          <h2>Hello, Friend!</h2>
          <p>Enter your personal details and start your journey</p>
          <button class="ghost" id="register">Sign Up</button>
        </div>

      </div>
    </div>

  </div>

  <script src="./public/js/auth.js"></script>
</body>
</html>