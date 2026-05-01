<?php
/**
 *  * CONTROLLER: LandingController
 * 
 */
namespace Controllers;

use Models\User as UserModel;
use Models\Product as ProductModel; 
use Models\Favorite as FavoriteModel;

class LandingController {

    private $ProductModel;
    private $favoriteModel;

    public function __construct() {
        $this->ProductModel = new ProductModel();
        $this->favoriteModel = new FavoriteModel();
    }

    public function index () {
        \view('pages/landing', []);
    }
    public function searching () {
        \view('pages/search', []);
    }
    public function aboutus () {
        \view('pages/aboutus', []);
    }
    public function profile () {
        \view('pages/profile', []);
    }
    public function list () {
        \view('pages/list', []);
    }
    public function login () {
        \view('pages/login', []);
    }
    public function register () {
        \view('pages/register', []);
    }
    public function admin () {
        \view('pages/admin', []);
    }
    public function favorit () {
        \view('pages/favorit', []);
    }

    public function favoritbarang() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $product_id = isset($data['product_id']) ? (int)$data['product_id'] : null;
        $platform   = isset($data['platform']) ? $data['platform'] : null;
        $user_id = $_SESSION['user_id'] ?? null;

        if (!$user_id || !$product_id || !$platform) {
            echo json_encode(['success' => false, 'error' => 'Invalid data (user_id, product_id, platform required)']);
            exit;
        }

        $exists = $this->favoriteModel->exists($user_id, $product_id, $platform);

        if ($exists) {
            $success = $this->favoriteModel->remove($user_id, $product_id, $platform);
            $message = 'Dihapus dari favorit';
        } else {
            $success = $this->favoriteModel->add($user_id, $product_id, $platform);
            $message = 'Ditambahkan ke favorit';
        }

        echo json_encode(['success' => $success, 'message' => $message]);
    }

    public function getFavorites() {
        header('Content-Type: application/json');
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }
        $favorites = $this->favoriteModel->getByUser($user_id);
        echo json_encode(['success' => true, 'data' => $favorites]);
    }

    public function getAllData() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Content-Type: application/json');
        echo json_encode($this->ProductModel->all());
        exit;
    }
}