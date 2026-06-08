<?php
/**
 * ============================================
 * HELPER FUNCTIONS - AUTHENTICATION & SESSION
 * Praktikum Aplikasi Web - Universitas Tidar
 * ============================================
 * 
 * Kumpulan fungsi helper untuk:
 * - Manajemen session (start, set, destroy)
 * - Cek status login & role user
 * - Flash messages (notifikasi sementara)
 * - CSRF protection
 * - Sanitasi input
 * - Activity logging
 */

// ═══════════════════════════════════════════
//  SESSION & LOGIN STATUS
// ═══════════════════════════════════════════

/** Memulai session jika belum aktif */
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/** Cek apakah user sudah login (ada user_id di session) */
function isLoggedIn() {
    startSession();
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/** Cek apakah user belum login (guest) */
function isGuest() {
    startSession();
    return empty($_SESSION['user_id']);
}

// ═══════════════════════════════════════════
//  CEK ROLE USER
// ═══════════════════════════════════════════

/** Cek apakah user adalah admin */
function isAdmin() {
    startSession();
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/** Cek apakah user adalah seller (penjual) */
function isSeller() {
    startSession();
    return isset($_SESSION['role']) && $_SESSION['role'] === 'seller';
}

/** Cek apakah user adalah super admin */
function isSuperAdmin() {
    startSession();
    return isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin';
}

/** Cek apakah user adalah user biasa (bukan admin/seller) */
function isStandardUser() {
    startSession();
    return isset($_SESSION['role']) && $_SESSION['role'] === 'user';
}

// ═══════════════════════════════════════════
//  DATA USER & SESSION MANAGEMENT
// ═══════════════════════════════════════════

/** Ambil data user yang sedang login dari session */
function getCurrentUser() {
    startSession();
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? '',
        'nama_lengkap' => $_SESSION['nama_lengkap'] ?? '',
        'email' => $_SESSION['email'] ?? '',
        'role' => $_SESSION['role'] ?? 'user'
    ];
}

/** Set session user setelah login berhasil */
function setUserSession($user) {
    startSession();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['login_time'] = time();
}

/** Hapus semua session dan cookie (proses logout) */
function destroySession() {
    startSession();

    // Kosongkan semua data session
    $_SESSION = [];

    // Hapus cookie session dari browser
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Hapus cookie "remember me" jika ada
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }

    session_destroy();
}

// ═══════════════════════════════════════════
//  GUARD / MIDDLEWARE MANUAL
// ═══════════════════════════════════════════

/** Paksa user login dulu, jika belum → redirect ke login */
function requireLogin() {
    if (!isLoggedIn()) {
        setFlashMessage('error', 'Silakan login terlebih dahulu.');
        redirect(\BASE_URL . 'login');
        exit;
    }
}

/** Paksa harus admin, jika bukan → redirect */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        setFlashMessage('error', 'Anda tidak memiliki akses ke halaman ini.');
        redirect(\BASE_URL . 'dashboard');
        exit;
    }
}

/** Paksa harus super admin, jika bukan → redirect */
function requireSuperAdmin() {
    requireLogin();
    if (!isSuperAdmin()) {
        setFlashMessage('error', 'Only Super Admin can access this page.');
        redirect(\BASE_URL . 'landing');
        exit;
    }
}

/** Paksa harus guest (belum login), jika sudah login → redirect */
function requireGuest() {
    if (isLoggedIn()) {
        setFlashMessage('error', 'Anda tidak memiliki akses ke halaman ini.');
        redirect(\BASE_URL . 'dashboard');
        exit;
    }
}

// ═══════════════════════════════════════════
//  FLASH MESSAGE (Notifikasi satu kali tampil)
// ═══════════════════════════════════════════

/** Simpan flash message ke session (akan ditampilkan sekali lalu hilang) */
function setFlashMessage($type, $message) {
    startSession();
    $_SESSION['flash'] = [
        'type' => $type,      // 'success' atau 'error'
        'message' => $message
    ];
}

/** Ambil flash message dari session dan langsung hapus */
function getFlashMessage() {
    startSession();
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']); // Hapus setelah diambil (hanya tampil sekali)
        return $flash;
    }
    return null;
}

/** Render flash message sebagai HTML alert (Bootstrap-style) */
function displayFlashMessage() {
    $flash = getFlashMessage();
    if ($flash) {
        $alertClass = $flash['type'] === 'success' ? 'alert-success' : 'alert-danger';
        echo '<div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($flash['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
    }
}

// ═══════════════════════════════════════════
//  REDIRECT & SECURITY
// ═══════════════════════════════════════════

/** Redirect browser ke URL lain */
function redirect($url) {
    header("Location: " . $url);
    exit;
}

/** Generate token CSRF (disimpan di session, dipakai di form) */
function generateCSRFToken() {
    startSession();
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Validasi token CSRF dari form submit (cegah serangan CSRF) */
function validateCSRFToken($token) {
    startSession();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/** Generate token random untuk fitur "Remember Me" */
function generateRememberToken() {
    return bin2hex(random_bytes(32));
}

/** Simpan token "Remember Me" di cookie browser */
function setRememberCookie($token) {
    setcookie('remember_token', $token, time() + COOKIE_LIFETIME, '/', '', false, true);
}

// ═══════════════════════════════════════════
//  SANITASI INPUT
// ═══════════════════════════════════════════

/** Bersihkan input user dari spasi berlebih, backslash, dan karakter HTML berbahaya */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/** Shortcut untuk escape output HTML (cegah XSS) */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// ═══════════════════════════════════════════
//  ACTIVITY LOG
// ═══════════════════════════════════════════

/** Catat aktivitas user ke tabel activity_logs (untuk audit trail) */
function logActivity($action, $description = '') {
    try {
        $db = getDB();
        $userId = $_SESSION['user_id'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $sql = "INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId, $action, $description, $ip]);
    } catch (\Exception $e) {
        // Gagal logging tidak boleh merusak alur utama aplikasi
    }
}