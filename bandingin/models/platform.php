<?php
/**
 * ============================================
 * MODEL: Platform (E-commerce Platform)
 * Banding.in - Perbandingan Harga E-commerce
 * ============================================
 * 
 * Menangani operasi database untuk tabel `platforms`.
 * Platform = e-commerce yang didukung (Tokopedia, Lazada, Blibli).
 * Admin bisa toggle aktif/nonaktif platform.
 */
namespace Models;

class Platform {
    private $db;                    // Object PDO koneksi database
    private $table = 'platforms';   // Nama tabel

    /** Constructor: ambil koneksi database */
    public function __construct() {
        $this->db = getDB();
    }

    /** Ambil semua platform yang aktif saja (untuk tampilan publik) */
    public function getAll() {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY name";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            // Fallback jika kolom is_active belum ada di tabel
            $sql = "SELECT * FROM {$this->table} ORDER BY name";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        }
    }

    /** Ambil semua platform termasuk yang nonaktif (untuk admin panel) */
    public function getAllIncludingInactive() {
        $sql = "SELECT * FROM {$this->table} ORDER BY name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /** Ambil platform berdasarkan ID */
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** Buat platform baru. Return ID platform atau false. */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, is_active) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([$data['name'], $data['is_active'] ?? 1]);
        if ($result) return $this->db->lastInsertId();
        return false;
    }

    /** Toggle status aktif/nonaktif platform (is_active = NOT is_active) */
    public function toggleActive($id) {
        $sql = "UPDATE {$this->table} SET is_active = NOT is_active WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}