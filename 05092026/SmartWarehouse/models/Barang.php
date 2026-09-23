<?php

class Barang {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getAllPaginated($limit, $offset, $search = '', $kategori_id = '') {
        $query = "SELECT b.*, k.name as kategori_name, COALESCE(SUM(s.qty), 0) as total_qty 
                  FROM barang b
                  LEFT JOIN kategori k ON b.kategori_id = k.id
                  LEFT JOIN stok s ON b.id = s.barang_id
                  WHERE (b.name LIKE :search OR b.code LIKE :search)";

        if (!empty($kategori_id)) {
            $query .= " AND b.kategori_id = :kategori_id";
        }

        $query .= " GROUP BY b.id ORDER BY b.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($query);
        $searchParam = "%$search%";
        $stmt->bindParam(':search', $searchParam);
        
        if (!empty($kategori_id)) {
            $stmt->bindParam(':kategori_id', $kategori_id);
        }

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalRows($search = '', $kategori_id = '') {
        $query = "SELECT COUNT(*) as total FROM barang b WHERE (b.name LIKE :search OR b.code LIKE :search)";
        
        if (!empty($kategori_id)) {
            $query .= " AND b.kategori_id = :kategori_id";
        }

        $stmt = $this->db->prepare($query);
        $searchParam = "%$search%";
        $stmt->bindParam(':search', $searchParam);
        
        if (!empty($kategori_id)) {
            $stmt->bindParam(':kategori_id', $kategori_id);
        }

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getById($id) {
        $query = "SELECT b.*, k.name as kategori_name, COALESCE(SUM(s.qty), 0) as total_qty 
                  FROM barang b
                  LEFT JOIN kategori k ON b.kategori_id = k.id
                  LEFT JOIN stok s ON b.id = s.barang_id
                  WHERE b.id = :id
                  GROUP BY b.id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function isCodeUnique($code, $excludeId = null) {
        $query = "SELECT COUNT(*) as count FROM barang WHERE code = :code";
        if ($excludeId) {
            $query .= " AND id != :excludeId";
        }
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':code', $code);
        if ($excludeId) {
            $stmt->bindParam(':excludeId', $excludeId);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] == 0;
    }

    public function create($data) {
        $query = "INSERT INTO barang (code, name, kategori_id, description, unit, min_stock, status) 
                  VALUES (:code, :name, :kategori_id, :description, :unit, :min_stock, :status)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':code', $data['code']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':kategori_id', $data['kategori_id']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':unit', $data['unit']);
        $stmt->bindParam(':min_stock', $data['min_stock']);
        $stmt->bindParam(':status', $data['status']);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $query = "UPDATE barang SET 
                  code = :code, 
                  name = :name, 
                  kategori_id = :kategori_id, 
                  description = :description, 
                  unit = :unit, 
                  min_stock = :min_stock, 
                  status = :status 
                  WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':code', $data['code']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':kategori_id', $data['kategori_id']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':unit', $data['unit']);
        $stmt->bindParam(':min_stock', $data['min_stock']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getByCode($code) {
        $query = "SELECT b.*, k.name as kategori_name 
                  FROM barang b
                  LEFT JOIN kategori k ON b.kategori_id = k.id
                  WHERE b.code = :code AND b.status = 'active'
                  LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllActive() {
        $query = "SELECT id, code, name, unit FROM barang WHERE status = 'active' ORDER BY name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Used by LaporanController/RiwayatController for filter dropdowns
    public function getAll() {
        $query = "SELECT id, code, name, unit FROM barang ORDER BY name ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        // Check relationships (e.g., in transactions or stock)
        $checkQuery = "SELECT COUNT(*) as count FROM transaksi_detail WHERE barang_id = :id";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bindParam(':id', $id);
        $checkStmt->execute();
        if ($checkStmt->fetch(PDO::FETCH_ASSOC)['count'] > 0) {
            return false; // Cannot delete, involved in transactions
        }
        
        $checkStock = "SELECT COALESCE(SUM(qty), 0) as total FROM stok WHERE barang_id = :id";
        $checkStockStmt = $this->db->prepare($checkStock);
        $checkStockStmt->bindParam(':id', $id);
        $checkStockStmt->execute();
        if ($checkStockStmt->fetch(PDO::FETCH_ASSOC)['total'] > 0) {
            return false; // Cannot delete, still has stock
        }

        $query = "DELETE FROM barang WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getAvailableLocations($barangId) {
        $query = "SELECT l.id, l.kode, l.nama, s.qty 
                  FROM stok s
                  JOIN lokasi l ON s.lokasi_id = l.id
                  WHERE s.barang_id = :barang_id AND s.qty > 0 AND l.status != 'tidak_aktif'
                  ORDER BY l.kode ASC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':barang_id', $barangId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
