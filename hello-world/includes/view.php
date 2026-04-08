<?php

/**
 * =====================================================
 * HELPER FUNCTIONS - VIEW CLASS
 * Praktikum Aplikasi Web - Universitas Tidar
 * =====================================================
 */

// View dengan sebuah main template
function viewWithMainTemplate($fileName,$data = [],  $mainName = 'layouts/main') {
    extract($data);

    // Buffering content
    ob_start();
    include VIEWS_PATH . $fileName . '.php';
    $content = ob_get_clean();

    include VIEWS_PATH . $mainName . ".php";
}

// View tanpa sebuah main template
function view ($fileName, $data = []) {
    extract($data);

    // Buffering content
    ob_start();
    include VIEWS_PATH . $fileName . '.php';
    $content = ob_get_clean();
    
    echo $content;
}

// Include komponen view
function includes ($fileName, $data = []) {
    extract($data);

    // Buffering content
    ob_start();
    include VIEWS_PATH . $fileName . '.php';
    $content = ob_get_clean();
    
    return $content;
}


// Deklarasi path js
function pathJs ($fileName) {
    return BASE_URL . 'public/js/' . $fileName . ".js";
}

// Deklarasi path css
function pathCss ($fileName) {
    return BASE_URL . 'public/css/' . $fileName . ".css";
}