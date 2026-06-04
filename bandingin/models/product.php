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

    public function getBySellerId($sellerId) {
        try {
            $sql = "SELECT p.*, pp.price, pp.link, pf.name AS platform_name, pf.id AS platform_id
                    FROM {$this->table} p
                    LEFT JOIN product_prices pp ON pp.product_id = p.id
                    LEFT JOIN platforms pf ON pp.platform_id = pf.id
                    WHERE p.seller_id = ?
                    ORDER BY p.created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$sellerId]);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function search($keyword) {
        $sql = "SELECT * FROM {$this->table} WHERE name LIKE ? AND (status = 'approved' OR status IS NULL) ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["%{$keyword}%"]);
        return $stmt->fetchAll();
    }

    public function all() {
        $sql = "SELECT 
        p.name AS product_name,
        p.id,
        p.category,
        pf.name AS platform_name,
        pp.price,
        pp.link
        FROM product_prices pp
        JOIN products p ON pp.product_id = p.id
        JOIN platforms pf ON pp.platform_id = pf.id
        WHERE (p.status = 'approved' OR p.status IS NULL)";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, category, seller_id, status) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $data['name'], 
            $data['category'] ?? null,
            $data['seller_id'] ?? null,
            $data['status'] ?? 'approved'
        ]);
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
        // First delete related product_prices
        $sql = "DELETE FROM product_prices WHERE product_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        // Then delete product
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getPending() {
        try {
            $sql = "SELECT p.*, u.username AS seller_name, pp.price, pp.link, pf.name AS platform_name
                    FROM {$this->table} p
                    LEFT JOIN users u ON p.seller_id = u.id
                    LEFT JOIN product_prices pp ON pp.product_id = p.id
                    LEFT JOIN platforms pf ON pp.platform_id = pf.id
                    WHERE p.status = 'pending'
                    ORDER BY p.created_at DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getAllWithStatus() {
        try {
            $sql = "SELECT p.*, u.username AS seller_name, pp.price, pp.link, pf.name AS platform_name
                    FROM {$this->table} p
                    LEFT JOIN users u ON p.seller_id = u.id
                    LEFT JOIN product_prices pp ON pp.product_id = p.id
                    LEFT JOIN platforms pf ON pp.platform_id = pf.id
                    ORDER BY p.created_at DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            // Fallback: simpler query without seller_id/status/created_at
            try {
                $sql = "SELECT p.*, pp.price, pp.link, pf.name AS platform_name
                        FROM {$this->table} p
                        LEFT JOIN product_prices pp ON pp.product_id = p.id
                        LEFT JOIN platforms pf ON pp.platform_id = pf.id
                        ORDER BY p.id DESC";
                $stmt = $this->db->query($sql);
                return $stmt->fetchAll();
            } catch (\Exception $e2) {
                return [];
            }
        }
    }

    public function updateStatus($id, $status) {
        $sql = "UPDATE {$this->table} SET status = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $id]);
    }

    public function bulkDeleteByPlatform($platformId) {
        $sql = "DELETE pp FROM product_prices pp WHERE pp.platform_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$platformId]);

        // Clean up orphaned products (products with no prices)
        $sql = "DELETE p FROM products p LEFT JOIN product_prices pp ON p.id = pp.product_id WHERE pp.id IS NULL";
        $stmt = $this->db->query($sql);

        return true;
    }

    public function countByStatus($status) {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table} WHERE status = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$status]);
            return $stmt->fetchColumn();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function count() {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchColumn();
    }
}