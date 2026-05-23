<?php
/**
 * LANGUAGE HELPER
 * Praktikum Aplikasi Web - Universitas Tidar
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set default language to English if not set
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}

function __($key) {
    $lang = $_SESSION['lang'];
    $langFile = ROOT_PATH . "lang/{$lang}.php";
    
    if (file_exists($langFile)) {
        $translations = include $langFile;
        if (isset($translations[$key])) {
            return $translations[$key];
        }
    }
    
    // Fallback to English if translation not found in ID
    if ($lang !== 'en') {
        $fallbackFile = ROOT_PATH . "lang/en.php";
        if (file_exists($fallbackFile)) {
            $fallbackTranslations = include $fallbackFile;
            if (isset($fallbackTranslations[$key])) {
                return $fallbackTranslations[$key];
            }
        }
    }

    return $key; // Return key if not found
}
