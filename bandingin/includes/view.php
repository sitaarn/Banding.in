<?php
/**
 * ============================================
 * HELPER FUNCTIONS - VIEW / TEMPLATE ENGINE
 * Praktikum Aplikasi Web - Universitas Tidar
 * ============================================
 * 
 * Fungsi-fungsi untuk merender view (halaman PHP).
 * Mendukung: layout template, view tanpa layout, include komponen,
 * dan helper path untuk asset CSS/JS.
 */

/**
 * Render view dengan layout utama (main template).
 * Content halaman akan di-inject ke dalam layout.
 * 
 * @param string $fileName - Path file view (tanpa .php), misal: 'pages/landing'
 * @param array $data - Data yang dikirim ke view (jadi variabel lokal)
 * @param string $mainName - Layout template, default: 'layouts/main'
 */
function viewWithMainTemplate($fileName, $data = [], $mainName = 'layouts/main') {
    extract($data); // Ubah array jadi variabel ($data['title'] → $title)

    // Buffer output halaman, simpan ke $content
    ob_start();
    include VIEWS_PATH . $fileName . '.php';
    $content = ob_get_clean();

    // Load layout utama (layout bisa echo $content di dalamnya)
    include VIEWS_PATH . $mainName . ".php";
}

/**
 * Render view langsung tanpa layout template.
 * Output langsung ditampilkan ke browser.
 */
function view($fileName, $data = []) {
    extract($data);

    ob_start();
    include VIEWS_PATH . $fileName . '.php';
    $content = ob_get_clean();
    
    echo $content;
}

/**
 * Include komponen view dan return sebagai string (bukan echo).
 * Berguna untuk menyusun partial/komponen dalam view lain.
 */
function includes($fileName, $data = []) {
    extract($data);

    ob_start();
    include VIEWS_PATH . $fileName . '.php';
    $content = ob_get_clean();
    
    return $content;
}

/**
 * Generate URL lengkap ke file JavaScript.
 * Contoh: pathJs('auth') → 'https://domain.com/bandingin/public/js/auth.js'
 */
function pathJs($fileName) {
    return BASE_URL . 'public/js/' . $fileName . ".js";
}

/**
 * Generate URL lengkap ke file CSS.
 * Contoh: pathCss('auth') → 'https://domain.com/bandingin/public/css/auth.css'
 */
function pathCss($fileName) {
    return BASE_URL . 'public/css/' . $fileName . ".css";
}