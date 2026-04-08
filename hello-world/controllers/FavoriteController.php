<?php
/**
 * =====================================================
 * CONTROLLER: FavoriteController
 * Menangani favorit user
 * Banding.in
 * =====================================================
 */
use Models\Favorite.php;
use Models\Product.php;

class FavoriteController {
    private $favoriteModel;
    private $productModel;

    public function __construct() {
        $this->favoriteModel = new Favorite();
        $this->productModel = new Product();
    }
 
    /**
     * GET /favorites
     */
    public function index() {
        header('Content-Type: application/json');
        requireLogin();

        $userId = $_SESSION['user_id'];
        $favorites = $this->favoriteModel->getUserFavorites($userId);
        echo json_encode(['success' => true, 'data' => $favorites]);
    }

    /**
     * POST /favorites/add
     */
    public function add() {
        header('Content-Type: application/json');
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        if (!$productId) {
            echo json_encode(['error' => 'Product ID diperlukan']);
            return;
        }

        // Cek produk ada
        $product = $this->productModel->getById($productId);
        if (!$product) {
            echo json_encode(['error' => 'Produk tidak ditemukan']);
            return;
        }

        $userId = $_SESSION['user_id'];
        if ($this->favoriteModel->add($userId, $productId)) {
            echo json_encode(['success' => true, 'message' => 'Produk ditambahkan ke favorit']);
        } else {
            echo json_encode(['error' => 'Gagal menambahkan atau sudah ada di favorit']);
        }
    }

    /**
     * POST /favorites/remove
     */
    public function remove() {
        header('Content-Type: application/json');
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        if (!$productId) {
            echo json_encode(['error' => 'Product ID diperlukan']);
            return;
        }

        $userId = $_SESSION['user_id'];
        if ($this->favoriteModel->remove($userId, $productId)) {
            echo json_encode(['success' => true, 'message' => 'Produk dihapus dari favorit']);
        } else {
            echo json_encode(['error' => 'Gagal menghapus favorit']);
        }
    }
}