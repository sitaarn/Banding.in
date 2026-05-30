<?php
/**
 * MODEL: ActivityLog
 * Menangani operasi database untuk tabel activity_logs
 */
namespace Models;

class ActivityLog {
    private $db;
    private $table = 'activity_logs';

    public function __construct() {
        $this->db = getDB();
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['user_id'] ?? null,
            $data['action'],
            $data['description'] ?? null,
            $data['ip_address'] ?? null
        ]);
    }

    public function getAll($limit = 100, $offset = 0) {
        try {
            $sql = "SELECT al.*, u.username, u.nama_lengkap 
                    FROM {$this->table} al 
                    LEFT JOIN users u ON al.user_id = u.id 
                    ORDER BY al.created_at DESC 
                    LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit, $offset]);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getRecent($limit = 10) {
        return $this->getAll($limit, 0);
    }

    public function count() {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchColumn();
    }
}
