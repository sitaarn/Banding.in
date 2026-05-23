<?php
/**
 * MODEL: ScraperLog
 * Menangani operasi database untuk tabel scraper_logs
 */
namespace Models;

class ScraperLog {
    private $db;
    private $table = 'scraper_logs';

    public function __construct() {
        $this->db = getDB();
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (platform_id, status, keyword, triggered_by) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $data['platform_id'] ?? null,
            $data['status'] ?? 'running',
            $data['keyword'] ?? null,
            $data['triggered_by'] ?? null
        ]);
        if ($result) return $this->db->lastInsertId();
        return false;
    }

    public function getAll($limit = 50) {
        $sql = "SELECT sl.*, pf.name AS platform_name, u.username AS triggered_by_name
                FROM {$this->table} sl
                LEFT JOIN platforms pf ON sl.platform_id = pf.id
                LEFT JOIN users u ON sl.triggered_by = u.id
                ORDER BY sl.started_at DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function updateStatus($id, $status, $itemsScraped = 0, $errorMessage = null) {
        $sql = "UPDATE {$this->table} SET status = ?, items_scraped = ?, error_message = ?, finished_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $itemsScraped, $errorMessage, $id]);
    }

    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
