<?php

class Lokasi {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // =============================================
    //  GENERATE kode from rack, level, posisi
    // =============================================
    public static function generateKode($rack, $level, $posisi) {
        return strtoupper(trim($rack)) . '.' . intval($level) . '.' . str_pad(intval($posisi), 2, '0', STR_PAD_LEFT);
    }

    // =============================================
    //  VALIDATE format kode: A.1.01
    // =============================================
    public static function isValidKodeFormat($kode) {
        return preg_match('/^[A-Z]\.\d{1,2}\.\d{2}$/', $kode);
    }

    // =============================================
    //  GET ALL (with items count, search, filter)
    // =============================================
    public function getAllPaginated($limit, $offset, $search = '', $rackFilter = '') {
        $query = "SELECT l.*,
                    COUNT(DISTINCT s.barang_id) as jumlah_item,
                    COALESCE(SUM(s.qty), 0) as total_qty
                  FROM lokasi l
                  LEFT JOIN stok s ON s.lokasi_id = l.id
                  WHERE (l.kode LIKE :search OR l.nama LIKE :search)
                    AND (:rack = '' OR l.rack = :rack)
                  GROUP BY l.id
                  ORDER BY l.rack ASC, l.level ASC, l.posisi ASC
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($query);
        $searchParam = "%$search%";
        $stmt->bindParam(':search', $searchParam);
        $stmt->bindParam(':rack', $rackFilter);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalRows($search = '', $rackFilter = '') {
        $query = "SELECT COUNT(*) as total FROM lokasi l
                  WHERE (l.kode LIKE :search OR l.nama LIKE :search)
                    AND (:rack = '' OR l.rack = :rack)";
        $stmt = $this->db->prepare($query);
        $searchParam = "%$search%";
        $stmt->bindParam(':search', $searchParam);
        $stmt->bindParam(':rack', $rackFilter);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // =============================================
    //  GET ALL for dropdown/filter
    // =============================================
    public function getAll() {
        $query = "SELECT * FROM lokasi ORDER BY rack, level, posisi";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =============================================
    //  GET DISTINCT RACKS
    // =============================================
    public function getDistinctRacks() {
        $query = "SELECT DISTINCT rack FROM lokasi ORDER BY rack ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    // =============================================
    //  GET BY ID (with items)
    // =============================================
    public function getById($id) {
        $query = "SELECT l.*,
                    COUNT(DISTINCT s.barang_id) as jumlah_item,
                    COALESCE(SUM(s.qty), 0) as total_qty
                  FROM lokasi l
                  LEFT JOIN stok s ON s.lokasi_id = l.id
                  WHERE l.id = :id
                  GROUP BY l.id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =============================================
    //  GET ITEMS AT LOCATION
    // =============================================
    public function getItemsByLokasiId($lokasi_id) {
        $query = "SELECT b.code, b.name, k.name as kategori, b.unit, s.qty, b.min_stock
                  FROM stok s
                  JOIN barang b ON b.id = s.barang_id
                  JOIN kategori k ON k.id = b.kategori_id
                  WHERE s.lokasi_id = :lokasi_id AND s.qty > 0
                  ORDER BY b.name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':lokasi_id', $lokasi_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =============================================
    //  CHECK KODE UNIQUE
    // =============================================
    public function isKodeUnique($kode, $excludeId = null) {
        $query = "SELECT COUNT(*) as count FROM lokasi WHERE kode = :kode";
        if ($excludeId) {
            $query .= " AND id != :id";
        }
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':kode', $kode);
        if ($excludeId) {
            $stmt->bindParam(':id', $excludeId);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] == 0;
    }

    // =============================================
    //  CREATE
    // =============================================
    public function create($data) {
        $query = "INSERT INTO lokasi (kode, rack, level, posisi, nama, deskripsi, kapasitas, status)
                  VALUES (:kode, :rack, :level, :posisi, :nama, :deskripsi, :kapasitas, :status)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':kode',      $data['kode']);
        $stmt->bindParam(':rack',      $data['rack']);
        $stmt->bindParam(':level',     $data['level']);
        $stmt->bindParam(':posisi',    $data['posisi']);
        $stmt->bindParam(':nama',      $data['nama']);
        $stmt->bindParam(':deskripsi', $data['deskripsi']);
        $stmt->bindParam(':kapasitas', $data['kapasitas']);
        $stmt->bindParam(':status',    $data['status']);
        return $stmt->execute();
    }

    // =============================================
    //  UPDATE
    // =============================================
    public function update($id, $data) {
        $query = "UPDATE lokasi SET
                    kode      = :kode,
                    rack      = :rack,
                    level     = :level,
                    posisi    = :posisi,
                    nama      = :nama,
                    deskripsi = :deskripsi,
                    kapasitas = :kapasitas,
                    status    = :status
                  WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':kode',      $data['kode']);
        $stmt->bindParam(':rack',      $data['rack']);
        $stmt->bindParam(':level',     $data['level']);
        $stmt->bindParam(':posisi',    $data['posisi']);
        $stmt->bindParam(':nama',      $data['nama']);
        $stmt->bindParam(':deskripsi', $data['deskripsi']);
        $stmt->bindParam(':kapasitas', $data['kapasitas']);
        $stmt->bindParam(':status',    $data['status']);
        $stmt->bindParam(':id',        $id);
        return $stmt->execute();
    }

    // =============================================
    //  DELETE (check stok first)
    // =============================================
    public function delete($id) {
        $checkQuery = "SELECT COALESCE(SUM(qty), 0) as total FROM stok WHERE lokasi_id = :id";
        $checkStmt  = $this->db->prepare($checkQuery);
        $checkStmt->bindParam(':id', $id);
        $checkStmt->execute();
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
        if ($result['total'] > 0) {
            return false; // masih ada stok
        }

        // Delete stok records with qty=0 first (orphan cleanup)
        $cleanStmt = $this->db->prepare("DELETE FROM stok WHERE lokasi_id = :id");
        $cleanStmt->bindParam(':id', $id);
        $cleanStmt->execute();

        $query = "DELETE FROM lokasi WHERE id = :id";
        $stmt  = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // =============================================
    //  STATS for dashboard
    // =============================================
    public function getStats() {
        $query = "SELECT
                    COUNT(*) as total,
                    SUM(status = 'kosong') as kosong,
                    SUM(status = 'terisi') as terisi,
                    SUM(status = 'tidak_aktif') as tidak_aktif,
                    COUNT(DISTINCT rack) as total_rack
                  FROM lokasi";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
