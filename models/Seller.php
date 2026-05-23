<?php
/**
 * 
 * MODEL: Seller
 * Menangani operasi database untuk produk milik seller
 * Banding.in
 * 
 */
namespace Models;

class Seller {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Mendapatkan semua produk milik seller tertentu
     */
    public function getMyProducts($sellerId) {
        $sql = "SELECT p.*, 
                       pp.price, pp.link as price_link, pp.platform_id,
                       pf.name as platform_name
                FROM products p
                LEFT JOIN product_prices pp ON p.id = pp.product_id
                LEFT JOIN platforms pf ON pp.platform_id = pf.id
                WHERE p.seller_id = ?
                ORDER BY p.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sellerId]);
        return $stmt->fetchAll();
    }

    /**
     * Menambahkan produk baru dari seller
     */
    public function addProduct($data) {
        try {
            $this->db->beginTransaction();

            // 1. Insert ke tabel products
            $sql = "INSERT INTO products (name, image, category, seller_id, platform, link) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['image'] ?? null,
                $data['category'],
                $data['seller_id'],
                $data['platform'],
                $data['link']
            ]);
            $productId = $this->db->lastInsertId();

            // 2. Insert ke tabel product_prices jika platform valid
            $platformId = $this->getPlatformId($data['platform']);
            if ($platformId) {
                $sqlPrice = "INSERT INTO product_prices (product_id, platform_id, price, link) 
                             VALUES (?, ?, ?, ?)";
                $stmtPrice = $this->db->prepare($sqlPrice);
                $stmtPrice->execute([
                    $productId,
                    $platformId,
                    $data['price'] ?? 0,
                    $data['link']
                ]);
            }

            $this->db->commit();
            return $productId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Mendapatkan platform ID berdasarkan nama
     */
    private function getPlatformId($platformName) {
        $sql = "SELECT id FROM platforms WHERE LOWER(name) = LOWER(?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$platformName]);
        $result = $stmt->fetch();
        return $result ? $result['id'] : null;
    }

    /**
     * Mendapatkan semua platform
     */
    public function getAllPlatforms() {
        $sql = "SELECT * FROM platforms ORDER BY name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Hapus produk milik seller
     */
    public function deleteProduct($productId, $sellerId) {
        // Pastikan produk milik seller ini
        $sql = "SELECT id FROM products WHERE id = ? AND seller_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId, $sellerId]);
        
        if (!$stmt->fetch()) {
            return false;
        }

        try {
            $this->db->beginTransaction();

            // Hapus dari product_prices dulu (foreign key)
            $sqlPrices = "DELETE FROM product_prices WHERE product_id = ?";
            $stmtPrices = $this->db->prepare($sqlPrices);
            $stmtPrices->execute([$productId]);

            // Hapus dari favorites
            $sqlFav = "DELETE FROM favorites WHERE product_id = ?";
            $stmtFav = $this->db->prepare($sqlFav);
            $stmtFav->execute([$productId]);

            // Hapus dari products
            $sqlProduct = "DELETE FROM products WHERE id = ? AND seller_id = ?";
            $stmtProduct = $this->db->prepare($sqlProduct);
            $stmtProduct->execute([$productId, $sellerId]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Hitung total produk seller
     */
    public function countMyProducts($sellerId) {
        $sql = "SELECT COUNT(*) FROM products WHERE seller_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sellerId]);
        return $stmt->fetchColumn();
    }
}
