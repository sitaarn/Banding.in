<?php 
/**
 * ============================================
 * INIT - Autoloader & Helper Umum
 * Praktikum Aplikasi Web - Universitas Tidar
 * ============================================
 * 
 * File ini berisi:
 * 1. PSR-4-like autoloader untuk memuat class otomatis
 * 2. Fungsi helper umum yang dipakai di berbagai tempat
 */

/**
 * Autoloader: Otomatis load file class berdasarkan namespace.
 * Contoh: `new Controllers\AuthController()` → require 'controllers/AuthController.php'
 * Namespace pertama jadi nama folder (lowercase), class terakhir jadi nama file.
 */
spl_autoload_register(function ($class) {
	$path = str_replace('\\', '/', $class);       // Ubah backslash namespace jadi slash
	$path = explode('/', $path);                   // Pecah jadi array [folder, class]
	$folder = strtolower($path[0]);                // Ambil folder (lowercase)
	$class = end($path);                           // Ambil nama class (bagian terakhir)
	require_once ROOT_PATH . "$folder" . "/" . "$class" . ".php";
});

/**
 * Mengambil inisial dari nama lengkap (maks 2 huruf).
 * Contoh: "John Doe" → "JD", "Alice" → "A"
 * Digunakan untuk avatar placeholder di UI.
 */
function get_initials($name) {
    $words = explode(' ', trim($name));
    $initials = '';
    foreach($words as $w) {
        if(!empty($w)) $initials .= strtoupper($w[0]);
        if(strlen($initials) >= 2) break; // Cukup 2 huruf saja
    }
    return $initials ? $initials : '?';
}
