<?php
/**
 *  * CONTROLLER: SellerController
 * 
 */
namespace Controllers;

use Models\Product as ProductModel; 
use Models\Product_Price as ProductPriceModel;

class SellerController {

    private $ProductModel;
    private $ProductPriceModel;

    public function __construct() {
        $this->ProductModel = new ProductModel();
        $this->ProductPriceModel = new ProductPriceModel();
    }

    public function addProduct() {
        $pageTitle = 'Tambah Produk (Seller)';
        \view('pages/seller/add', [
            'pageTitle' => $pageTitle
        ]);
    }

    public function storeProduct() {
        // Only allow POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(\BASE_URL . 'seller/add');
        }

        $name = sanitize($_POST['name'] ?? '');
        $platform_id = sanitize($_POST['platform_id'] ?? '');
        $link = sanitize($_POST['link'] ?? '');
        $price = sanitize($_POST['price'] ?? 0);
        $category = sanitize($_POST['category'] ?? 'Lainnya');
        
        // E-commerce names corresponding to platform_id
        $platforms = [
            2 => 'tokopedia',
            3 => 'lazada',
            4 => 'blibli'
        ];

        // Validations
        if (empty($name) || empty($platform_id) || empty($link) || empty($price)) {
            setFlashMessage('error', 'Semua field wajib diisi.');
            redirect(\BASE_URL . 'seller/add');
        }

        if (!array_key_exists($platform_id, $platforms)) {
            setFlashMessage('error', 'Platform tidak valid.');
            redirect(\BASE_URL . 'seller/add');
        }

        $platform_name = $platforms[$platform_id];

        // Validation for link and platform
        if (stripos($link, $platform_name) === false) {
            setFlashMessage('error', 'Link gagal disimpan: e-commerce tidak cocok dengan link yang diberikan.');
            redirect(\BASE_URL . 'seller/add');
        }

        // Insert Product
        $productId = $this->ProductModel->create([
            'name' => $name,
            'category' => $category,
            'seller_id' => $_SESSION['user_id'] ?? null,
            'status' => 'pending'
        ]);

        if ($productId) {
            // Insert Product Price
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
    public function myProducts() {
        $sellerId = $_SESSION['user_id'] ?? null;
        $products = $this->ProductModel->getBySellerId($sellerId);
        
        \view('pages/seller/products', [
            'pageTitle' => 'Kelola Produk Saya',
            'products' => $products
        ]);
    }

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
        
        // Verify the product belongs to this seller
        $product = $this->ProductModel->getById($productId);
        if (!$product || ($product['seller_id'] ?? null) != $sellerId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Product not found or unauthorized']);
            return;
        }
        
        $result = $this->ProductModel->delete($productId);
        logActivity('product_delete', "Seller deleted product: {$product['name']}");
        
        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
    }
}
