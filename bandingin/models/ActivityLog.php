<?php
/**
 * ============================================
 * MODEL: ActivityLog (Log Aktivitas Sistem)
 * Banding.in - Perbandingan Harga E-commerce
 * ============================================
 * 
 * Mencatat semua aktivitas penting di sistem untuk audit trail.
 * Contoh: login, ubah role, hapus produk, trigger scraper, dll.
 * Data disimpan di tabel `activity_logs`.
 */
namespace Models;

class ActivityLog {
    private $db;                       // Object PDO koneksi database
    private $table = 'activity_logs';  // Nama tabel

    /** Constructor: ambil koneksi database */
    public function __construct() {
        $this->db = getDB();
    }

    /** Catat aktivitas baru ke database */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['user_id'] ?? null,
            $data['action'],           // Jenis aksi (misal: 'role_change', 'product_delete')
            $data['description'] ?? null, // Deskripsi detail
            $data['ip_address'] ?? null   // IP address user
        ]);
    }

    /** Ambil log aktivitas (dengan data user), urutkan dari terbaru. Support pagination. */
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

    /** Ambil log terbaru (shortcut untuk getAll dengan limit tertentu) */
    public function getRecent($limit = 10) {
        return $this->getAll($limit, 0);
    }

    /** Hitung total semua log */
    public function count() {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchColumn();
    }
}
