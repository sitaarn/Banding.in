<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $pageTitle ?? 'Reset Password' ?></title>
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/auth.css">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
  <div class="auth-container">
    <div class="container" id="container">
      <div class="form-container sign-in-container" style="width: 100%; z-index: 10; opacity: 1;">
        <form action="<?= BASE_URL . 'reset-password' ?>" method="post">
          <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
          <h2 style="margin-bottom: 20px;">Reset Password</h2>
          
          <input type="password" name="password" placeholder="New Password" required autofocus style="margin-bottom: 15px;">
          <input type="password" name="confirm_password" placeholder="Confirm New Password" required style="margin-bottom: 15px;">
          
          <button type="submit" class="primary-btn">RESET PASSWORD</button>
        </form>
      </div>
    </div>
  </div>

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
  ?>
  <style>
      .swal-custom {
          border: 1px solid rgba(255,255,255,0.1);
          box-shadow: 0 8px 32px rgba(0,0,0,0.5);
      }
      .container {
          max-width: 500px;
          min-height: 400px;
      }
  </style>
</body>
</html>
