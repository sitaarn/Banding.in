<?php
/**
 * ============================================
 * MODEL: ScraperLog (Log Scraping)
 * Banding.in - Perbandingan Harga E-commerce
 * ============================================
 * 
 * Mencatat setiap kali scraper dijalankan.
 * Menyimpan: platform, keyword, status (running/done/failed),
 * jumlah item yang di-scrape, dan error message.
 */
namespace Models;

class ScraperLog {
    private $db;                      // Object PDO koneksi database
    private $table = 'scraper_logs';  // Nama tabel

    /** Constructor: ambil koneksi database */
    public function __construct() {
        $this->db = getDB();
    }

    /** Buat entry log scraper baru (dipanggil saat trigger scrape). Return ID log atau false. */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (platform_id, status, keyword, triggered_by) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $data['platform_id'] ?? null,
            $data['status'] ?? 'running',     // Status awal: running
            $data['keyword'] ?? null,          // Keyword pencarian
            $data['triggered_by'] ?? null      // User ID yang trigger
        ]);
        if ($result) return $this->db->lastInsertId();
        return false;
    }

    /** Ambil semua log scraper (dengan nama platform dan user), urutkan dari terbaru */
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

    /**
     * Update status scraper setelah selesai (dipanggil dari script Python).
     * Set status (done/failed), jumlah item, error message, dan waktu selesai.
     */
    public function updateStatus($id, $status, $itemsScraped = 0, $errorMessage = null) {
        $sql = "UPDATE {$this->table} SET status = ?, items_scraped = ?, error_message = ?, finished_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $itemsScraped, $errorMessage, $id]);
    }

    /** Ambil log scraper berdasarkan ID */
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
