<?php
/**
 * ============================================
 * CONTROLLER: SellerController
 * Praktikum Aplikasi Web - Universitas Tidar
 * ============================================
 * 
 * Controller khusus untuk fitur Seller (penjual):
 * - Tampilkan form tambah produk
 * - Simpan produk baru (status pending, perlu verifikasi admin)
 * - Lihat daftar produk milik seller
 * - Hapus produk milik seller
 */
namespace Controllers;

use Models\Product as ProductModel; 
use Models\Product_Price as ProductPriceModel;

class SellerController {

    private $ProductModel;      // Model untuk tabel products
    private $ProductPriceModel; // Model untuk tabel product_prices

    /** Constructor: inisialisasi model produk dan harga */
    public function __construct() {
        $this->ProductModel = new ProductModel();
        $this->ProductPriceModel = new ProductPriceModel();
    }

    /** Tampilkan form tambah produk baru */
    public function addProduct() {
        $pageTitle = 'Tambah Produk (Seller)';
        \view('pages/seller/add', [
            'pageTitle' => $pageTitle
        ]);
    }

    /**
     * Proses simpan produk baru dari form seller.
     * 
     * Alur:
     * 1. Validasi input (nama, platform, link, harga)
     * 2. Cek link sesuai dengan platform yang dipilih
     * 3. Insert ke tabel products (status = pending)
     * 4. Insert harga ke tabel product_prices
     * 5. Log aktivitas & redirect
     */
    public function storeProduct() {
        // Hanya terima request POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(\BASE_URL . 'seller/add');
        }

        $name = sanitize($_POST['name'] ?? '');
        $platform_id = sanitize($_POST['platform_id'] ?? '');
        $link = sanitize($_POST['link'] ?? '');
        $price = sanitize($_POST['price'] ?? 0);
        $category = sanitize($_POST['category'] ?? 'Lainnya');
        
        // Mapping platform_id ke nama e-commerce (untuk validasi link)
        $platforms = [
            2 => 'tokopedia',
            3 => 'lazada',
            4 => 'blibli'
        ];

        // Validasi: semua field wajib diisi
        if (empty($name) || empty($platform_id) || empty($link) || empty($price)) {
            setFlashMessage('error', 'Semua field wajib diisi.');
            redirect(\BASE_URL . 'seller/add');
        }

        // Validasi: platform harus valid
        if (!array_key_exists($platform_id, $platforms)) {
            setFlashMessage('error', 'Platform tidak valid.');
            redirect(\BASE_URL . 'seller/add');
        }

        $platform_name = $platforms[$platform_id];

        // Validasi: link harus mengandung nama platform (cegah link palsu)
        if (stripos($link, $platform_name) === false) {
            setFlashMessage('error', 'Link gagal disimpan: e-commerce tidak cocok dengan link yang diberikan.');
            redirect(\BASE_URL . 'seller/add');
        }

        // Insert produk ke database (status pending, menunggu verifikasi admin)
        $productId = $this->ProductModel->create([
            'name' => $name,
            'category' => $category,
            'seller_id' => $_SESSION['user_id'] ?? null,
            'status' => 'pending'
        ]);

        if ($productId) {
            // Insert harga dan link produk
            $this->ProductPriceModel->create([
                'product_id' => $productId,
                'platform_id' => $platform_id,
                'price' => $price,
                'link' => $link
            ]);
            
            logActivity('product_add', "Seller added product: {$name} (pending verification)");
            setFlashMessage('success', 'Product submitted! Waiting for admin verification.');
            redirect(\BASE_URL . 'seller/add');
        } else {
            setFlashMessage('error', 'Failed to add product.');
            redirect(\BASE_URL . 'seller/add');
        }
    }

    /** Tampilkan daftar semua produk milik seller yang sedang login */
    public function myProducts() {
        $sellerId = $_SESSION['user_id'] ?? null;
        $products = $this->ProductModel->getBySellerId($sellerId);
        
        \view('pages/seller/products', [
            'pageTitle' => 'Kelola Produk Saya',
            'products' => $products
        ]);
    }

    /**
     * Hapus produk milik seller (via AJAX).
     * Cek kepemilikan produk sebelum hapus (seller hanya bisa hapus produknya sendiri).
     */
    public function deleteProduct() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $productId = $data['product_id'] ?? null;
        $sellerId = $_SESSION['user_id'] ?? null;
        
        if (!$productId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Product ID required']);
            return;
        }
        
        // Verifikasi produk ini milik seller yang sedang login
        $product = $this->ProductModel->getById($productId);
        if (!$product || ($product['seller_id'] ?? null) != $sellerId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Product not found or unauthorized']);
            return;
        }
        
        // Hapus produk dan catat log
        $result = $this->ProductModel->delete($productId);
        logActivity('product_delete', "Seller deleted product: {$product['name']}");
        
        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
    }
}
