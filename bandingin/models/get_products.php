<?php
/**
 * ============================================
 * API ENDPOINT: Get All Products (Standalone)
 * Banding.in - Perbandingan Harga E-commerce
 * ============================================
 * 
 * Endpoint PHP mandiri (tidak lewat router) untuk mengambil
 * semua data produk beserta harga dan platform.
 * Digunakan oleh halaman list.php via fetch() JavaScript.
 * 
 * Output: JSON array produk dengan harga dari berbagai platform.
 * Hanya menampilkan produk yang approved atau status NULL.
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');  // Izinkan CORS (untuk development)
error_reporting(0);                         // Sembunyikan error PHP dari output
ini_set('display_errors', 0);

// Koneksi langsung ke database (tidak pakai Singleton karena file ini standalone)
$host = 'localhost';
$dbname = 'bandingin';
$username = 'root';
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query: gabungkan products + product_prices + platforms
    // Filter hanya platform aktif dan produk yang approved/null
    $sql = "SELECT 
                p.id, 
                p.name AS product_name, 
                p.category, 
                pp.price, 
                pp.link, 
                pl.name AS platform_name
            FROM products p
            INNER JOIN product_prices pp ON p.id = pp.product_id
            INNER JOIN platforms pl ON pp.platform_id = pl.id
            WHERE pl.is_active = 1
              AND (p.status = 'approved' OR p.status IS NULL)
            ORDER BY p.id DESC"; // DESC agar hasil scraping terbaru muncul duluan

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "DB Error: " . $e->getMessage()]);
}
?>