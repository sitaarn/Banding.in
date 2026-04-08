<?php
/**
 * =====================================================
 * MODEL: Platform
 * Menangani operasi database untuk tabel platforms
 * Banding.in
 * =====================================================
 */
namesapce Models;

class Platform {
    private $db;
    private $table = 'platforms';

    public function __construct() {
        $this->db = getDB();
    }

    public function getAll() {
        $sql = "SELECT * FROM {$this->table} ORDER BY name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}