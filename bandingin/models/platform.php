<?php
/**
 * 
 * MODEL: Platform
 * Menangani operasi database untuk tabel platforms
 * Banding.in
 * 
 */
namespace Models;

class Platform {
    private $db;
    private $table = 'platforms';

    public function __construct() {
        $this->db = getDB();
    }

    public function getAll() {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getAllIncludingInactive() {
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

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (name, is_active) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([$data['name'], $data['is_active'] ?? 1]);
        if ($result) return $this->db->lastInsertId();
        return false;
    }

    public function toggleActive($id) {
        $sql = "UPDATE {$this->table} SET is_active = NOT is_active WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}