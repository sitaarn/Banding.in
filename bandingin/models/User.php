<?php
/**
 * 
 * Menangani operasi database untuk tabel users
 * Praktikum Aplikasi Web - Universitas Tidar
 * 
 */
namespace Models;

class User {
    private $db;
    private $table = 'users';

    /**
     * Constructor
     */
    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Mendapatkan semua user
     */
    public function getAll() {
        $sql = "SELECT id, username, email, nama_lengkap, role, created_at
                FROM {$this->table}
                ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Mendapatkan user berdasarkan ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Mendapatkan user berdasarkan username
     */
    public function getByUsername($username) {
        $sql = "SELECT * FROM {$this->table} WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    /**
     * Mendapatkan user berdasarkan email
     */
    public function getByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Mendapatkan user berdasarkan remember token
     */
    public function getByRememberToken($token) {
        $sql = "SELECT * FROM {$this->table} WHERE remember_token = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    /**
     * Cek apakah username sudah ada
     */
    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE username = ?";
        $params = [$username];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Cek apakah email sudah ada
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE email = ?";
        $params = [$email];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Membuat user baru
     */
    public function create($data) {
        $role = $data['role'] ?? 'user';
        $sql = "INSERT INTO {$this->table} (username, email, password, nama_lengkap, role)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['nama_lengkap'],
            $role
        ]);

        if ($result) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Update user
     */
    public function update($id, $data) {
        $fields = [];
        $params = [];

        // Build query dinamis
        foreach ($data as $key => $value) {
            if ($key === 'password') {
                $fields[] = "{$key} = ?";
                $params[] = password_hash($value, PASSWORD_DEFAULT);
            } else {
                $fields[] = "{$key} = ?";
                $params[] = $value;
            }
        }

        $params[] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Update remember token
     */
    public function updateRememberToken($id, $token) {
        $sql = "UPDATE {$this->table} SET remember_token = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$token, $id]);
    }

    /**
     * Hapus remember token
     */
    public function clearRememberToken($id) {
        return $this->updateRememberToken($id, null);
    }

    /**
     * Hapus user
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Verifikasi password
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Autentikasi user (login)
     */
    public function authenticate($username, $password) {
        // Cari user berdasarkan username atau email
        $sql = "SELECT * FROM {$this->table} WHERE username = ? OR email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user && $this->verifyPassword($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    /**
     * Hitung total user
     */
    public function count() {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchColumn();
    }

    public function countByRole($role) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE role = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$role]);
        return $stmt->fetchColumn();
    }

    public function updateRole($id, $role) {
        $sql = "UPDATE {$this->table} SET role = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$role, $id]);
    }

    public function toggleActive($id) {
        $sql = "UPDATE {$this->table} SET is_active = NOT is_active WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function resetPassword($id, $defaultPassword = 'password123') {
        $sql = "UPDATE {$this->table} SET password = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([password_hash($defaultPassword, PASSWORD_DEFAULT), $id]);
    }

    public function getAllWithStats() {
        $sql = "SELECT u.id, u.username, u.nama_lengkap, u.email, u.role, u.is_active, u.created_at,
                (SELECT COUNT(*) FROM products p WHERE p.seller_id = u.id) AS product_count,
                (SELECT COUNT(*) FROM favorites f WHERE f.user_id = u.id) AS favorite_count
                FROM {$this->table} u
                ORDER BY u.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function setResetToken($email, $token, $expires) {
        $sql = "UPDATE {$this->table} SET reset_token = ?, reset_token_expires = ? WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$token, $expires, $email]);
    }

    public function getByResetToken($token) {
        $sql = "SELECT * FROM {$this->table} WHERE reset_token = ? AND reset_token_expires > NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    public function clearResetToken($id) {
        $sql = "UPDATE {$this->table} SET reset_token = NULL, reset_token_expires = NULL WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
