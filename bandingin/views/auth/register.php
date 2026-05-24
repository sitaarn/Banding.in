<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="public/css/style.css" rel="stylesheet">
    <link href="public/css/list.css" rel="stylesheet">
    <style>
        /* Override specific list.css to make sure register form is centered and works well */
        body {
            display: block;
            min-height: 100vh;
            color: white;
        }
        .container {
            position: relative;
            z-index: 10;
        }
        .card {
            background: rgba(255,255,255,0.05);
            border: 1.5px solid rgba(255,255,255,0.12);
            backdrop-filter: blur(16px);
            border-radius: 24px;
            color: white;
        }
        .text-muted { color: rgba(255,255,255,0.6) !important; }
        h2 { color: white; font-family: 'DM Serif Display', serif; }
        .form-control, .form-check-input {
            background: rgba(255,255,255,0.06);
            border: 1.5px solid rgba(255,255,255,0.12);
            color: white;
        }
        .form-control:focus, .form-check-input:focus {
            background: rgba(255,255,255,0.1);
            border-color: #2ecad0;
            color: white;
            box-shadow: 0 0 0 4px rgba(46,202,208,0.15);
        }
        .form-label { color: rgba(255,255,255,0.85); font-weight: 500; }
        .btn-outline-secondary { border-color: rgba(255,255,255,0.12); color: rgba(255,255,255,0.7); }
        .btn-outline-secondary:hover { background: rgba(255,255,255,0.1); color: white; }
        .btn-primary { background: linear-gradient(135deg, #2ecad0, #2d5a9e); border: none; border-radius: 999px; padding: 12px 24px; font-weight: 600; letter-spacing: 0.03em; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(46,202,208,0.4); background: linear-gradient(135deg, #2ecad0, #2d5a9e); }
        .form-control::placeholder { color: rgba(255,255,255,0.4); }
        .form-check-input:checked { background-color: #2ecad0; border-color: #2ecad0; }
    </style>
</head>
<body>
    <div class="bg"></div>
    <div class="blobs">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>
    <div class="orbs">
        <div class="orb orb-1"></div><div class="orb orb-2"></div><div class="orb orb-3"></div>
        <div class="orb orb-4"></div><div class="orb orb-5"></div><div class="orb orb-6"></div>
        <div class="orb orb-7"></div>
    </div>
    <div class="grid-overlay"></div>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-5">
            <div class="col-md-6 col-lg-5">
                <div class="text-center mb-4">
                    <i class="bi bi-mortarboard-fill text-primary" style="font-size: 4rem;"></i>
                    <h2 class="mt-2"><?= APP_NAME ?></h2>
                    <p class="text-muted">Buat akun baru untuk memulai</p>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= e($errors['general']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="index.php?page=register" id="registerForm" novalidate>
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="bi bi-person me-1"></i>Username <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
                                       id="username"
                                       name="username"
                                       value="<?= e($old['username'] ?? '') ?>"
                                       placeholder="Masukkan username"
                                       required
                                       autofocus>
                                <div class="invalid-feedback">
                                    <?= e($errors['username'] ?? 'Username wajib diisi.') ?>
                                </div>
                                <small class="text-muted">Minimal 4 karakter, hanya huruf dan angka.</small>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope me-1"></i>Email <span class="text-danger">*</span>
                                </label>
                                <input type="email"
                                       class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                       id="email"
                                       name="email"
                                       value="<?= e($old['email'] ?? '') ?>"
                                       placeholder="Masukkan email"
                                       required>
                                <div class="invalid-feedback">
                                    <?= e($errors['email'] ?? 'Format email tidak valid.') ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label">
                                    <i class="bi bi-card-text me-1"></i>Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control <?= isset($errors['nama_lengkap']) ? 'is-invalid' : '' ?>"
                                       id="nama_lengkap"
                                       name="nama_lengkap"
                                       value="<?= e($old['nama_lengkap'] ?? '') ?>"
                                       placeholder="Masukkan nama lengkap"
                                       required>
                                <div class="invalid-feedback">
                                    <?= e($errors['nama_lengkap'] ?? 'Nama lengkap wajib diisi.') ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock me-1"></i>Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                           id="password"
                                           name="password"
                                           placeholder="Masukkan password"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <div class="invalid-feedback">
                                        <?= e($errors['password'] ?? 'Password minimal 6 karakter.') ?>
                                    </div>
                                </div>
                                <small class="text-muted">Minimal 6 karakter.</small>
                            </div>

                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">
                                    <i class="bi bi-lock-fill me-1"></i>Konfirmasi Password <span class="text-danger">*</span>
                                </label>
                                <input type="password"
                                       class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                                       id="confirm_password"
                                       name="confirm_password"
                                       placeholder="Ulangi password"
                                       required>
                                <div class="invalid-feedback">
                                    <?= e($errors['confirm_password'] ?? 'Konfirmasi password tidak cocok.') ?>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-person-plus me-2"></i>Daftar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <p class="text-muted">
                        Sudah punya akun?
                        <a href="index.php?page=login" class="text-primary text-decoration-none">
                            Login di sini
                        </a>
                    </p>
                </div>

                <div class="text-center mt-4">
                    <small class="text-muted">
                        &copy; <?= date('Y') ?> Universitas Tidar
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/validasi.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');

            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });

        // Real-time validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            if (this.value !== password) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    </script>
</body>
</html>
