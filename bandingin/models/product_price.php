<?php
/**
 *  * MODEL: ProductPrice
 * Menangani operasi database untuk tabel product_prices
 * Banding.in
 * 
 */
namespace Models; 

class Product_Price {
    private $db;
    private $table = 'product_prices';

    public function __construct() {
        $this->db = getDB();
    }

    public function getByProductId($productId) {
        $sql = "SELECT pp.*, pf.name as platform_name
                FROM {$this->table} pp
                JOIN platforms pf ON pp.platform_id = pf.id
                WHERE pp.product_id = ?
                ORDER BY pp.price ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public function getByProductAndPlatform($productId, $platformId) {
        $sql = "SELECT * FROM {$this->table} WHERE product_id = ? AND platform_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId, $platformId]);
        return $stmt->fetch();
    }

    public function search($keyword, $platformIds = [], $minPrice = 0, $maxPrice = 999999999) {
        // Gabung dengan products dan platforms
        $sql = "SELECT pp.*, p.id as product_id, p.name as product_name, p.image, p.category,
                       pf.id as platform_id, pf.name as platform_name
                FROM {$this->table} pp
                JOIN products p ON pp.product_id = p.id
                JOIN platforms pf ON pp.platform_id = pf.id
                WHERE p.name LIKE ? AND pp.price BETWEEN ? AND ?";

        $params = ["%{$keyword}%", $minPrice, $maxPrice];

        if (!empty($platformIds)) {
            $placeholders = implode(',', array_fill(0, count($platformIds), '?'));
            $sql .= " AND pp.platform_id IN ($placeholders)";
            $params = array_merge($params, $platformIds);
        }

        $sql .= " ORDER BY pp.price ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function all($keyword="", $platformIds = [], $minPrice = 0, $maxPrice = 999999999) {
        // Gabung dengan products dan platforms
        $sql = "SELECT pp.*, p.id as product_id, p.name as product_name, p.image, p.category,
                       pf.id as platform_id, pf.name as platform_name
                FROM {$this->table} pp
                JOIN products p ON pp.product_id = p.id
                JOIN platforms pf ON pp.platform_id = pf.id";

        $params = ["%{$keyword}%", $minPrice, $maxPrice];

        if (!empty($platformIds)) {
            $placeholders = implode(',', array_fill(0, count($platformIds), '?'));
            $sql .= " AND pp.platform_id IN ($placeholders)";
            $params = array_merge($params, $platformIds);
        }

        $sql .= " ORDER BY pp.price ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (product_id, platform_id, price, link) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$data['product_id'], $data['platform_id'], $data['price'], $data['link']]);
    }

    public function update($id, $data) {
        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = ?";
            $params[] = $value;
        }
        $params[] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}