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
        $this->ensureTableExists();
    }

    private function ensureTableExists() {
        try {
            $this->db->query("SELECT 1 FROM {$this->table} LIMIT 1");
        } catch (\Exception $e) {
            // Table doesn't exist, create it
            try {
                $this->db->exec("
                    CREATE TABLE IF NOT EXISTS `{$this->table}` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `product_id` int(11) NOT NULL,
                        `product_name_snapshot` varchar(255) DEFAULT NULL,
                        `platform_id` int(11) DEFAULT NULL,
                        `reporter_id` int(11) DEFAULT NULL,
                        `reason` varchar(255) DEFAULT NULL,
                        `status` enum('open','reviewed','dismissed') DEFAULT 'open',
                        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                        PRIMARY KEY (`id`),
                        KEY `product_id` (`product_id`),
                        KEY `reporter_id` (`reporter_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
                ");
            } catch (\Exception $e2) {
                // Silently fail
            }
        }
    }

    public function create($data) {
        try {
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
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getAll($limit = 100, $offset = 0) {
        try {
            $sql = "SELECT pr.*, COALESCE(p.name, pr.product_name_snapshot, '(Barang Telah Dihapus)') AS product_name, u.username AS reporter_username,
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
        } catch (\Exception $e) {
            return [];
        }
    }

    public function countByProduct($productId) {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table} WHERE product_id = ? AND status = 'open'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$productId]);
            return $stmt->fetchColumn();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function updateStatus($id, $status) {
        $sql = "UPDATE {$this->table} SET status = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $id]);
    }

    public function countOpen() {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table} WHERE status = 'open'";
            $stmt = $this->db->query($sql);
            return $stmt->fetchColumn();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function hasUserReported($productId, $userId) {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table} WHERE product_id = ? AND reporter_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$productId, $userId]);
            return $stmt->fetchColumn() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function markAsReviewedAndSnapshot($productId, $productName) {
        $sql = "UPDATE {$this->table} SET status = 'reviewed', product_name_snapshot = ? WHERE product_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$productName, $productId]);
    }
}
