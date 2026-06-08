<?php
/**
 * ============================================
 * MODEL: Product_Price (Harga Produk per Platform)
 * Banding.in - Perbandingan Harga E-commerce
 * ============================================
 * 
 * Menangani operasi database untuk tabel `product_prices`.
 * Setiap produk bisa punya beberapa harga dari platform berbeda
 * (Tokopedia, Lazada, Blibli). Inilah inti fitur "bandingin".
 */
namespace Models; 

class Product_Price {
    private $db;                        // Object PDO koneksi database
    private $table = 'product_prices';  // Nama tabel

    /** Constructor: ambil koneksi database */
    public function __construct() {
        $this->db = getDB();
    }

    /** Ambil semua harga untuk satu produk (dari semua platform), urut dari termurah */
    public function getByProductId($productId) {
        $sql = "SELECT pp.*, pf.name as platform_name
                FROM {$this->table} pp
                JOIN platforms pf ON pp.platform_id = pf.id
                WHERE pp.product_id = ?
                ORDER BY pp.price ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    /** Ambil harga spesifik untuk satu produk di satu platform */
    public function getByProductAndPlatform($productId, $platformId) {
        $sql = "SELECT * FROM {$this->table} WHERE product_id = ? AND platform_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$productId, $platformId]);
        return $stmt->fetch();
    }

    /**
     * Cari harga produk berdasarkan keyword, platform, dan range harga.
     * Join: product_prices → products → platforms
     * Hasilnya diurutkan dari harga termurah.
     */
    public function search($keyword, $platformIds = [], $minPrice = 0, $maxPrice = 999999999) {
        $sql = "SELECT pp.*, p.id as product_id, p.name as product_name, p.category,
                       pf.id as platform_id, pf.name as platform_name
                FROM {$this->table} pp
                JOIN products p ON pp.product_id = p.id
                JOIN platforms pf ON pp.platform_id = pf.id
                WHERE p.name LIKE ? AND pp.price BETWEEN ? AND ?";

        $params = ["%{$keyword}%", $minPrice, $maxPrice];

        // Filter berdasarkan platform tertentu (opsional)
        if (!empty($platformIds)) {
            $placeholders = implode(',', array_fill(0, count($platformIds), '?'));
            $sql .= " AND pp.platform_id IN ($placeholders)";
            $params = array_merge($params, $platformIds);
        }

        $sql .= " ORDER BY pp.price ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Ambil semua harga produk (mirip search tapi tanpa WHERE clause utama) */
    public function all($keyword="", $platformIds = [], $minPrice = 0, $maxPrice = 999999999) {
        $sql = "SELECT pp.*, p.id as product_id, p.name as product_name, p.category,
                       pf.id as platform_id, pf.name as platform_name
                FROM {$this->table} pp
                JOIN products p ON pp.product_id = p.id
                JOIN platforms pf ON pp.platform_id = pf.id";

        $params = ["%{$keyword}%", $minPrice, $maxPrice];

        if (!empty($platformIds)) {
            $placeholders = implode(',', array_fill(0, count($platformIds), '?'));
            $sql .= " AND pp.platform_id IN ($placeholders)";
            $params = array_merge($params, $platformIds);
        }

        $sql .= " ORDER BY pp.price ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Insert harga produk baru (product_id, platform_id, price, link) */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (product_id, platform_id, price, link) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$data['product_id'], $data['platform_id'], $data['price'], $data['link']]);
    }

    /** Update data harga produk (dinamis) */
    public function update($id, $data) {
        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = ?";
            $params[] = $value;
        }
        $params[] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /** Hapus satu record harga produk */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}