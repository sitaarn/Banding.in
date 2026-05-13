<?php
/**
 * 
 * MODEL: Product
 * Menangani operasi database untuk tabel products
 * Banding.in
 * 
 */
namespace Models;

class Product {
    private $db;
    private $table = 'products';

    public function __construct() {
        $this->db = getDB();
    }

    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function search($keyword) {
        $sql = "SELECT * FROM {$this->table} WHERE name LIKE ? ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["%{$keyword}%"]);
        return $stmt->fetchAll();
    }

    public function all() {
        $sql = "SELECT 
        p.name AS product_name,
        p.image,
        p.id,
        p.category,
        pf.name AS platform_name,
        pp.price,
        pp.link
        FROM product_prices pp
        JOIN products p ON pp.product_id = p.id
        JOIN platforms pf ON pp.platform_id = pf.id";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, image, category) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([$data['name'], $data['image'] ?? null, $data['category'] ?? null]);
        if ($result) return $this->db->lastInsertId();
        return false;
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