<?php
/**
 * CONTROLLER: SellerController
 * Menangani halaman dan operasi seller
 * Banding.in
 */
namespace Controllers;

use Models\Seller as SellerModel;

class SellerController {
    private $sellerModel;

    public function __construct() {
        $this->sellerModel = new SellerModel();
    }

    /**
     * Halaman Keuntungan Menjadi Seller
     */
    public function benefits() {
        \view('pages/seller_benefits', [
            'pageTitle' => 'Keuntungan Menjadi Seller'
        ]);
    }

    /**
     * Dashboard Seller
     */
    public function dashboard() {
        \requireSeller();

        $sellerId = $_SESSION['user_id'];
        $products = $this->sellerModel->getMyProducts($sellerId);
        $platforms = $this->sellerModel->getAllPlatforms();
        $totalProducts = $this->sellerModel->countMyProducts($sellerId);

        \view('pages/seller_dashboard', [
            'pageTitle' => 'Seller Dashboard',
            'products' => $products,
            'platforms' => $platforms,
            'totalProducts' => $totalProducts
        ]);
    }

    /**
     * Simpan produk baru
     */
    public function storeProduct() {
        \requireSeller();

        $errors = [];
        $sellerId = $_SESSION['user_id'];

        // Validasi
        $validator = validate($_POST);
        $validator->required('name', 'Nama produk wajib diisi.')
            ->required('platform', 'Platform/ecommerce wajib dipilih.')
            ->required('category', 'Kategori wajib diisi.')
            ->required('link', 'Link produk wajib diisi.');

        if ($validator->isValid()) {
            $data = [
                'name' => sanitize($_POST['name']),
                'category' => sanitize($_POST['category']),
                'platform' => sanitize($_POST['platform']),
                'link' => sanitize($_POST['link']),
                'price' => isset($_POST['price']) ? (int)$_POST['price'] : 0,
                'image' => null,
                'seller_id' => $sellerId
            ];

            $productId = $this->sellerModel->addProduct($data);

            if ($productId) {
                setFlashMessage('success', 'Produk berhasil ditambahkan!');
            } else {
                setFlashMessage('error', 'Gagal menambahkan produk.');
            }
        } else {
            $errors = $validator->getErrors();
            $_SESSION['errors_messages'] = $errors;
        }

        redirect(\BASE_URL . 'seller/dashboard');
    }

    /**
     * Hapus produk
     */
    public function deleteProduct() {
        \requireSeller();

        $sellerId = $_SESSION['user_id'];
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

        if ($productId > 0) {
            $result = $this->sellerModel->deleteProduct($productId, $sellerId);
            if ($result) {
                setFlashMessage('success', 'Produk berhasil dihapus.');
            } else {
                setFlashMessage('error', 'Gagal menghapus produk.');
            }
        }

        redirect(\BASE_URL . 'seller/dashboard');
    }
}
