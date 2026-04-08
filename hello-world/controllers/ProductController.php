<?php
/**
 * =====================================================
 * CONTROLLER: ProductController
 * Menangani pencarian dan detail produk
 * Banding.in
 * =====================================================
 */

use Models\Product.php;
use Models\ProductPrice.php;
use Models\Platform.php;

class ProductController {
    private $productModel;
    private $priceModel;
    private $platformModel;

    public function __construct() {
        $this->productModel = new Product();
        $this->priceModel = new ProductPrice();
        $this->platformModel = new Platform();
    }

    /**
     * GET /search?q=...&platform[]=1&platform[]=2&min=...&max=...&sort=...
     */
    public function search() {
        header('Content-Type: application/json');

        $query = isset($_GET['q']) ? trim($_GET['q']) : '';
        $platformIds = isset($_GET['platform']) ? (array)$_GET['platform'] : [];
        $minPrice = isset($_GET['min']) ? (int)$_GET['min'] : 0;
        $maxPrice = isset($_GET['max']) ? (int)$_GET['max'] : 999999999;
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'cheapest';

        if (empty($query)) {
            echo json_encode(['error' => 'Query pencarian harus diisi']);
            return;
        }

        // Jika platformIds kosong, ambil semua platform
        if (empty($platformIds)) {
            $platforms = $this->platformModel->getAll();
            $platformIds = array_column($platforms, 'id');
        }

        // Ambil data harga
        $results = $this->priceModel->search($query, $platformIds, $minPrice, $maxPrice);

        // Kelompokkan per produk
        $products = [];
        foreach ($results as $row) {
            $pid = $row['product_id'];
            if (!isset($products[$pid])) {
                $products[$pid] = [
                    'id' => $pid,
                    'name' => $row['product_name'],
                    'image' => $row['image'],
                    'category' => $row['category'],
                    'prices' => []
                ];
            }
            $products[$pid]['prices'][] = [
                'platform_id' => $row['platform_id'],
                'platform_name' => $row['platform_name'],
                'price' => $row['price'],
                'link' => $row['link']
            ];
        }

        // Hitung harga termurah per produk
        foreach ($products as &$prod) {
            $prices = array_column($prod['prices'], 'price');
            $prod['cheapest_price'] = min($prices);
            $prod['cheapest_platform'] = $prod['prices'][array_search($prod['cheapest_price'], $prices)]['platform_name'];
        }

        // Urutkan
        if ($sort === 'cheapest') {
            usort($products, fn($a, $b) => $a['cheapest_price'] <=> $b['cheapest_price']);
        } elseif ($sort === 'expensive') {
            usort($products, fn($a, $b) => $b['cheapest_price'] <=> $a['cheapest_price']);
        } elseif ($sort === 'name') {
            usort($products, fn($a, $b) => strcmp($a['name'], $b['name']));
        }

        echo json_encode(['success' => true, 'data' => array_values($products)]);
    }

    /**
     * GET /product?id=1
     */
    public function detail() {
        header('Content-Type: application/json');
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$id) {
            echo json_encode(['error' => 'ID produk tidak valid']);
            return;
        }

        $product = $this->productModel->getById($id);
        if (!$product) {
            echo json_encode(['error' => 'Produk tidak ditemukan']);
            return;
        }

        $prices = $this->priceModel->getByProductId($id);
        $product['prices'] = $prices;

        // Hitung termurah
        if (!empty($prices)) {
            $cheapest = min(array_column($prices, 'price'));
            $product['cheapest_price'] = $cheapest;
        }

        echo json_encode(['success' => true, 'data' => $product]);
    }

    /**
     * GET /platforms (opsional, untuk mendapatkan daftar platform)
     */
    public function platforms() {
        header('Content-Type: application/json');
        $platforms = $this->platformModel->getAll();
        echo json_encode(['success' => true, 'data' => $platforms]);
    }
}