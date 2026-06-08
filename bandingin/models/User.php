<?php
/**
 * ============================================
 * MODEL: User
 * Praktikum Aplikasi Web - Universitas Tidar
 * ============================================
 * 
 * Menangani semua operasi database untuk tabel `users`.
 * CRUD user, autentikasi, role management, remember me token,
 * reset password, dan statistik user.
 */
namespace Models;

class User {
    private $db;              // Object PDO koneksi database
    private $table = 'users'; // Nama tabel di database

    /** Constructor: ambil koneksi database via Singleton */
    public function __construct() {
        $this->db = getDB();
    }

    // ═══════════════════════════════════════════
    //  READ - Ambil data user
    // ═══════════════════════════════════════════

    /** Ambil semua user (tanpa password), urutkan dari terbaru */
    public function getAll() {
        $sql = "SELECT id, username, email, nama_lengkap, role, created_at
                FROM {$this->table}
                ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /** Ambil user berdasarkan ID (semua kolom, termasuk password hash) */
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /** Ambil user berdasarkan username */
    public function getByUsername($username) {
        $sql = "SELECT * FROM {$this->table} WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    /** Ambil user berdasarkan email */
    public function getByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /** Ambil user berdasarkan remember_token (untuk auto-login via cookie) */
    public function getByRememberToken($token) {
        $sql = "SELECT * FROM {$this->table} WHERE remember_token = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    // ═══════════════════════════════════════════
    //  CEK DUPLIKAT
    // ═══════════════════════════════════════════

    /** Cek apakah username sudah ada (opsional: exclude ID tertentu untuk edit profil) */
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

    /** Cek apakah email sudah ada (opsional: exclude ID tertentu untuk edit profil) */
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

    // ═══════════════════════════════════════════
    //  CREATE & UPDATE
    // ═══════════════════════════════════════════

    /** Buat user baru. Password otomatis di-hash. Return ID user baru atau false. */
    public function create($data) {
        $role = $data['role'] ?? 'user';
        $sql = "INSERT INTO {$this->table} (username, email, password, nama_lengkap, role)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT), // Hash password
            $data['nama_lengkap'],
            $role
        ]);
        if ($result) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /** Update data user (dinamis, hanya update field yang dikirim). Password otomatis di-hash. */
    public function update($id, $data) {
        $fields = [];
        $params = [];
        // Build query dinamis berdasarkan data yang dikirim
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

    /** Update remember_token untuk fitur "Remember Me" */
    public function updateRememberToken($id, $token) {
        $sql = "UPDATE {$this->table} SET remember_token = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$token, $id]);
    }

    /** Hapus remember_token (saat logout) */
    public function clearRememberToken($id) {
        return $this->updateRememberToken($id, null);
    }

    /** Hapus user dari database */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    // ═══════════════════════════════════════════
    //  AUTENTIKASI & PASSWORD
    // ═══════════════════════════════════════════

    /** Verifikasi password plain dengan hash (wrapper password_verify) */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Autentikasi login: cari user berdasarkan username ATAU email,
     * lalu verifikasi password. Return data user atau false.
     */
    public function authenticate($username, $password) {
        $sql = "SELECT * FROM {$this->table} WHERE username = ? OR email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user && $this->verifyPassword($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    // ═══════════════════════════════════════════
    //  ADMIN: Statistik & Role Management
    // ═══════════════════════════════════════════

    /** Hitung total semua user */
    public function count() {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchColumn();
    }

    /** Hitung jumlah user berdasarkan role tertentu (misal: 'seller', 'admin') */
    public function countByRole($role) {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table} WHERE role = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$role]);
            return $stmt->fetchColumn();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /** Ubah role user (user/seller/admin) */
    public function updateRole($id, $role) {
        $sql = "UPDATE {$this->table} SET role = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$role, $id]);
    }

    /** Toggle status aktif/nonaktif user (is_active = NOT is_active) */
    public function toggleActive($id) {
        $sql = "UPDATE {$this->table} SET is_active = NOT is_active WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    /** Reset password user ke default (password123) */
    public function resetPassword($id, $defaultPassword = 'password123') {
        $sql = "UPDATE {$this->table} SET password = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([password_hash($defaultPassword, PASSWORD_DEFAULT), $id]);
    }

    /** Ambil semua user dengan statistik (jumlah produk & favorit per user) untuk admin panel */
    public function getAllWithStats() {
        try {
            $sql = "SELECT u.id, u.username, u.nama_lengkap, u.email, u.role, u.is_active, u.created_at,
                    (SELECT COUNT(*) FROM products p WHERE p.seller_id = u.id) AS product_count,
                    (SELECT COUNT(*) FROM favorites f WHERE f.user_id = u.id) AS favorite_count
                    FROM {$this->table} u
                    ORDER BY u.created_at DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            // Fallback: query sederhana jika kolom role/is_active belum ada
            try {
                $sql = "SELECT u.id, u.username, u.nama_lengkap, u.email, u.created_at
                        FROM {$this->table} u
                        ORDER BY u.id DESC";
                $stmt = $this->db->query($sql);
                return $stmt->fetchAll();
            } catch (\Exception $e2) {
                return [];
            }
        }
    }

    // ═══════════════════════════════════════════
    //  RESET PASSWORD (via token, fitur lupa password)
    // ═══════════════════════════════════════════

    /** Simpan token reset password dan waktu kedaluwarsa */
    public function setResetToken($email, $token, $expires) {
        $sql = "UPDATE {$this->table} SET reset_token = ?, reset_token_expires = ? WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$token, $expires, $email]);
    }

    /** Ambil user berdasarkan reset token (yang masih berlaku) */
    public function getByResetToken($token) {
        $sql = "SELECT * FROM {$this->table} WHERE reset_token = ? AND reset_token_expires > NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    /** Hapus reset token setelah password berhasil direset */
    public function clearResetToken($id) {
        $sql = "UPDATE {$this->table} SET reset_token = NULL, reset_token_expires = NULL WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
