<?php
/**
 * ============================================
 * LANGUAGE HELPER - Sistem Multi-Bahasa
 * Praktikum Aplikasi Web - Universitas Tidar
 * ============================================
 * 
 * Mengelola terjemahan teks di aplikasi.
 * Default bahasa: Indonesia (id).
 * Bahasa yang didukung: Indonesia (id) dan English (en).
 * 
 * File terjemahan ada di folder lang/ (id.php dan en.php).
 * Penggunaan di view: <?= __('welcome_message') ?>
 */

// Pastikan session aktif (bahasa disimpan di session)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set bahasa default ke Indonesia jika belum diset
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'id';
}

/**
 * Fungsi terjemahan: ambil teks berdasarkan key.
 * 
 * Alur:
 * 1. Cari di file bahasa yang aktif (misal: lang/id.php)
 * 2. Jika tidak ditemukan, fallback ke English (lang/en.php)
 * 3. Jika tetap tidak ditemukan, return key apa adanya
 * 
 * @param string $key - Kunci terjemahan (misal: 'nav_home', 'btn_search')
 * @return string - Teks terjemahan atau key jika tidak ditemukan
 */
function __($key) {
    $lang = $_SESSION['lang'];
    $langFile = ROOT_PATH . "lang/{$lang}.php";
    
    // Cari di bahasa aktif
    if (file_exists($langFile)) {
        $translations = include $langFile;
        if (isset($translations[$key])) {
            return $translations[$key];
        }
    }
    
    // Fallback ke English jika terjemahan tidak ditemukan di bahasa aktif
    if ($lang !== 'en') {
        $fallbackFile = ROOT_PATH . "lang/en.php";
        if (file_exists($fallbackFile)) {
            $fallbackTranslations = include $fallbackFile;
            if (isset($fallbackTranslations[$key])) {
                return $fallbackTranslations[$key];
            }
        }
    }

    return $key; // Jika tidak ditemukan di manapun, return key-nya
}
