<?php

/**
 * ============================================
 * CONTROLLER: AuthController
 * Praktikum Aplikasi Web - Universitas Tidar
 * ============================================
 * 
 * Menangani semua proses autentikasi:
 * - Login (validasi kredensial, set session)
 * - Register (buat user baru)
 * - Logout (hapus session)
 * - Profil (lihat & edit profil user)
 * - Ganti password
 */

namespace Controllers;

use Models\User as UserModel;

class AuthController
{
    private $userModel; // Instance model User untuk query database

    /** Constructor: inisialisasi model User */
    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Tampilkan halaman login.
     * Jika sudah login → redirect ke landing.
     * Jika ada cookie remember_token → auto-login.
     */
    public function loginView()
    {
        if (isLoggedIn()) {
            redirect(\BASE_URL . 'landing');
        }

        // Cek cookie "remember me" untuk auto-login
        if (isset($_COOKIE['remember_token'])) {
            $user = $this->userModel->getByRememberToken($_COOKIE['remember_token']);
            if ($user) {
                setUserSession($user);
                redirect(\BASE_URL . 'landing');
            }
        }

        $error = $_SESSION['errors_messages'] ?? '';

        $pageTitle = 'Login';
        \view('auth/login', [
            'pageTitle' => $pageTitle,
            'error' => $error
        ]);
    }

    /**
     * Proses autentikasi login.
     * Validasi input → cek kredensial → set session → redirect.
     * Juga handle role checking (user vs seller) dan "remember me".
     */
    public function authenticaton()
    {
        $username = sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        // Validasi input
        $validator = validate($_POST);
        $validator->required('username', 'Username wajib diisi.')->required('password', 'Password wajib diisi.');

        if ($validator->isValid()) {
            // Cek username/email + password di database
            $user = $this->userModel->authenticate($username, $password);

            if ($user) {
                $loginRole = $_POST['role'] ?? 'user';

                // Cegah user biasa login di form seller, dan sebaliknya
                if ($loginRole === 'seller' && $user['role'] !== 'seller') {
                    setFlashMessage('error', 'Gagal: Akun ini bukan akun Seller.');
                    redirect(\BASE_URL . 'login');
                    return;
                }
                
                if ($loginRole === 'user' && $user['role'] === 'seller') {
                    setFlashMessage('error', 'Gagal: Akun ini adalah akun Seller.');
                    redirect(\BASE_URL . 'login');
                    return;
                }

                // Set session user
                setUserSession($user);

                // Handle "remember me" → simpan token ke DB dan cookie
                if ($remember) {
                    $token = generateRememberToken();
                    $this->userModel->updateRememberToken($user['id'], $token);
                    setRememberCookie($token);
                }

                // Redirect berdasarkan role
                if ($user['role'] === 'super_admin' || $user['role'] === 'admin') {
                    redirect(\BASE_URL . 'admin/dashboard');
                    return;
                }

                redirect(\BASE_URL . 'landing');
            } else {
                setFlashMessage('error', 'Username dan password salah.');
                redirect(\BASE_URL . 'login');
            }
        } else {
            $_SESSION['errors_messages'] = $validator->getFirstError();
            redirect(\BASE_URL . 'login');
        }
    }


    /**
     * Tampilkan halaman register.
     * Jika sudah login → redirect.
     */
    public function register()
    {
        if (isLoggedIn()) {
            redirect(\BASE_URL . 'landing');
        }

        $errors = $_SESSION['errors_messages'] ?? [];
        $old = $_SESSION['old_messages'] ?? [];

        $pageTitle = 'Register';
        \view('auth/register', [
            'pageTitle' => $pageTitle,
            'errors' => $errors,
            'old' => $old
        ]);
    }

    /**
     * Proses registrasi user baru.
     * Validasi semua field → cek duplikat username/email → simpan ke DB.
     * Mendukung role 'user' (pembeli) dan 'seller' (penjual).
     */
    public function storeUser()
    {
        $errors = [];
        $old = [
            'username' => sanitize($_POST['username'] ?? ''),
            'email' => sanitize($_POST['email'] ?? ''),
            'nama_lengkap' => sanitize($_POST['nama_lengkap'] ?? '')
        ];

        // Validasi semua field
        $validator = validate($_POST);
        $validator->required('username', 'Username wajib diisi.')
            ->minLength('username', 4, 'Username minimal 4 karakter.')
            ->maxLength('username', 50, 'Username maksimal 50 karakter.')
            ->alphanumeric('username', 'Username hanya boleh huruf dan angka.')
            ->required('email', 'Email wajib diisi.')
            ->email('email', 'Format email tidak valid.')
            ->required('nama_lengkap', 'Nama lengkap wajib diisi.')
            ->minLength('nama_lengkap', 3, 'Nama lengkap minimal 3 karakter.')
            ->required('password', 'Password wajib diisi.')
            ->minLength('password', 8, 'Password minimal 8 karakter.')
            ->required('confirm_password', 'Konfirmasi password wajib diisi.')
            ->matches('confirm_password', 'password', 'Konfirmasi password tidak cocok.');

        if ($validator->isValid()) {
            // Cek duplikat username
            if ($this->userModel->usernameExists($old['username'])) {
                $errors['username'] = 'Username sudah digunakan.';
            }

            // Cek duplikat email
            if ($this->userModel->emailExists($old['email'])) {
                $errors['email'] = 'Email sudah digunakan.';
            }

            // Password tidak boleh sama dengan username
            if (empty($errors)) {
                if ($_POST['password'] === $old['username']) {
                    $errors['password'] = 'Password tidak boleh sama dengan username.';
                }
            }

            if (empty($errors)) {
                // Tentukan role: seller jika dipilih, default user
                $role = isset($_POST['role']) && $_POST['role'] === 'seller' ? 'seller' : 'user';
                
                // Simpan user baru ke database
                try {
                    $userId = $this->userModel->create([
                        'username' => $old['username'],
                        'email' => $old['email'],
                        'nama_lengkap' => $old['nama_lengkap'],  
                        'password' => $_POST['password'],
                        'role' => $role
                    ]);
                } catch (\PDOException $e) {
                    $errors['general'] = 'Gagal mendaftar, mungkin terjadi kesalahan pada database (misal: duplikat).';
                    $userId = false;
                }

                if ($userId) {
                    setFlashMessage('success', 'Registrasi berhasil! Silakan login.');
                    redirect(\BASE_URL . 'login');
                } else if (empty($errors['general'])) {
                    $errors['general'] = 'Terjadi kesalahan saat registrasi.';
                }
            }
        } else {
            $errors = $validator->getErrors();
        }

        // Simpan error dan old input ke session, lalu redirect balik
        $_SESSION['errors_messages'] = $errors;
        $_SESSION['old_messages'] = $old;
        redirect(\BASE_URL . 'login');
    }

    /**
     * Proses logout: hapus session & cookie, redirect ke login.
     */
    public function logout()
    {
        destroySession();
        setFlashMessage('success', 'Anda telah berhasil logout.');
        redirect(\BASE_URL . 'login');
    }

    /**
     * Tampilkan halaman profil user yang sedang login.
     * Ambil data user dari DB dan render view profile.
     */
    public function profile()
    {
        requireLogin();

        $user = $this->userModel->getById($_SESSION['user_id']);
        $errors = $_SESSION['errors_messages'] ?? [];
        $success = $_SESSION['success_messages'] ?? '';

        $pageTitle = 'Profil Saya';
        \view('pages/profile', [
            'pageTitle' => $pageTitle,
            'errors' => $errors,
            'success' => $success,
            'user' => $user
        ]);
    }

    /**
     * Update data profil (nama, email, dan opsional password baru).
     * Validasi → cek duplikat email → update di DB → refresh session.
     */
    public function updateProfil()
    {
        $user = $this->userModel->getById($_SESSION['user_id']);
        $errors = [];
        $success = '';

        $nama_lengkap = sanitize($_POST['nama_lengkap'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $new_password = $_POST['new_password'] ?? '';

        // Validasi input
        $validator = validate($_POST);
        $validator->required('nama_lengkap', 'Nama lengkap wajib diisi.')
            ->required('email', 'Email wajib diisi.')
            ->email('email', 'Format email tidak valid.');

        // Validasi password baru (jika diisi)
        if (!empty($new_password)) {
            $validator->minLength('new_password', 8, 'Password baru minimal 8 karakter.');
            if ($new_password === $user['username']) {
                $errors['new_password'] = 'Password tidak boleh sama dengan username.';
            }
        }

        if ($validator->isValid() && empty($errors['new_password'])) {
            // Cek email duplikat (kecuali email sendiri)
            if ($this->userModel->emailExists($email, $user['id'])) {
                $errors['email'] = 'Email sudah digunakan.';
            } else {
                $dataToUpdate = [
                    'nama_lengkap' => $nama_lengkap,
                    'email' => $email
                ];
                
                // Tambahkan password baru jika diisi
                if (!empty($new_password)) {
                    $dataToUpdate['password'] = $new_password;
                }

                // Update di database
                $this->userModel->update($user['id'], $dataToUpdate);

                // Update session agar navbar, dll langsung berubah
                $_SESSION['nama_lengkap'] = $nama_lengkap;
                $_SESSION['email'] = $email;

                $success = 'Profil berhasil diperbarui.';
                $user = $this->userModel->getById($user['id']);
            }
        } else {
            $errors = $validator->getErrors();
        }

        $_SESSION['errors_messages'] = $errors;
        $_SESSION['success_messages'] = $success;
        redirect(\BASE_URL . 'profile');
    }

    /**
     * Ganti password (halaman terpisah dari profil).
     * Cek password lama → validasi password baru → update di DB.
     */
    public function changePassword()
    {
        $user = $this->userModel->getById($_SESSION['user_id']);
        $errors = [];
        $success = '';

        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        $validator = validate($_POST);
        $validator->required('current_password', 'Password saat ini wajib diisi.')
            ->required('new_password', 'Password baru wajib diisi.')
            ->minLength('new_password', 8, 'Password baru minimal 8 karakter.')
            ->required('confirm_password', 'Konfirmasi password wajib diisi.')
            ->matches('confirm_password', 'new_password', 'Konfirmasi password tidak cocok.');

        if ($validator->isValid()) {
            // Verifikasi password lama
            if (!$this->userModel->verifyPassword($current_password, $user['password'])) {
                $errors['current_password'] = 'Password saat ini salah.';
            } else {
                $this->userModel->update($user['id'], [
                    'password' => $new_password
                ]);
                $success = 'Password berhasil diubah.';
            }
        } else {
            $errors = $validator->getErrors();
        }

        $_SESSION['errors_messages'] = $errors;
        $_SESSION['success_messages'] = $success;
        redirect(\BASE_URL . 'profile');
    }
}
