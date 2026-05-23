<?php
/**
 * MODEL: ProductReport
 * Menangani operasi database untuk tabel product_reports
 */
namespace Models;

class ProductReport {
    private $db;
    private $table = 'product_reports';

    public function __construct() {
        $this->db = getDB();
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (product_id, platform_id, reporter_id, reason) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $data['product_id'],
            $data['platform_id'] ?? null,
            $data['reporter_id'] ?? null,
            $data['reason'] ?? null
        ]);
        if ($result) return $this->db->lastInsertId();
        return false;
    }

    public function getAll($limit = 100, $offset = 0) {
        $sql = "SELECT pr.*, p.name AS product_name, u.username AS reporter_username,
                       pf.name AS platform_name
                FROM {$this->table} pr
                LEFT JOIN products p ON pr.product_id = p.id
                LEFT JOIN users u ON pr.reporter_id = u.id
                LEFT JOIN platforms pf ON pr.platform_id = pf.id
                ORDER BY pr.created_at DESC
                LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    public function countByProduct($productId) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE product_id = ? AND status = 'open'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetchColumn();
    }

    public function updateStatus($id, $status) {
        $sql = "UPDATE {$this->table} SET status = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $id]);
    }

    public function countOpen() {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE status = 'open'";
        $stmt = $this->db->query($sql);
        return $stmt->fetchColumn();
    }

    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
