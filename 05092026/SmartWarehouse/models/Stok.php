<?php
require_once __DIR__ . '/../config/database.php';

class Stok {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function getStockReport($search = '', $kategori_id = '', $lokasi_id = '', $status = '', $limit = 10, $offset = 0) {
        $params = [];
        
        $sql = "SELECT b.id, b.code, b.name, b.unit, b.min_stock, k.name as kategori_name,
                       COALESCE(SUM(s.qty), 0) as total_qty
                FROM barang b
                LEFT JOIN kategori k ON b.kategori_id = k.id
                LEFT JOIN stok s ON b.id = s.barang_id ";
                
        $whereClauses = [];
        if (!empty($lokasi_id)) {
            // If location is filtered, we only sum up stock for that specific location
            // We use a subquery or join condition. It's safer to just do a WHERE on the joined table
            $whereClauses[] = "s.lokasi_id = :lokasi";
            $params[':lokasi'] = $lokasi_id;
        }

        if (!empty($search)) {
            $whereClauses[] = "(b.code LIKE :search OR b.name LIKE :search)";
            $params[':search'] = "%$search%";
        }

        if (!empty($kategori_id)) {
            $whereClauses[] = "b.kategori_id = :kategori";
            $params[':kategori'] = $kategori_id;
        }

        if (count($whereClauses) > 0) {
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }

        $sql .= " GROUP BY b.id, b.code, b.name, b.unit, b.min_stock, k.name ";

        // HAVING clause for Status
        if (!empty($status)) {
            if ($status === 'AMAN') {
                $sql .= " HAVING total_qty > b.min_stock ";
            } else if ($status === 'MINIMUM') {
                $sql .= " HAVING total_qty <= b.min_stock AND total_qty > 0 ";
            } else if ($status === 'HABIS') {
                $sql .= " HAVING total_qty = 0 ";
            }
        }

        $sql .= " ORDER BY b.code ASC ";
        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        
        // Bind params carefully because LIMIT/OFFSET need integers
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countStockReport($search = '', $kategori_id = '', $lokasi_id = '', $status = '') {
        $params = [];
        
        // Count with GROUP BY requires wrapping in a subquery
        $sql = "SELECT COUNT(*) as total_rows FROM (
                    SELECT b.id, COALESCE(SUM(s.qty), 0) as total_qty, b.min_stock
                    FROM barang b
                    LEFT JOIN stok s ON b.id = s.barang_id ";
        
        $whereClauses = [];
        if (!empty($lokasi_id)) {
            $whereClauses[] = "s.lokasi_id = :lokasi";
            $params[':lokasi'] = $lokasi_id;
        }

        if (!empty($search)) {
            $whereClauses[] = "(b.code LIKE :search OR b.name LIKE :search)";
            $params[':search'] = "%$search%";
        }

        if (!empty($kategori_id)) {
            $whereClauses[] = "b.kategori_id = :kategori";
            $params[':kategori'] = $kategori_id;
        }

        if (count($whereClauses) > 0) {
            $sql .= " WHERE " . implode(" AND ", $whereClauses);
        }

        $sql .= " GROUP BY b.id, b.min_stock ";

        if (!empty($status)) {
            if ($status === 'AMAN') {
                $sql .= " HAVING total_qty > b.min_stock ";
            } else if ($status === 'MINIMUM') {
                $sql .= " HAVING total_qty <= b.min_stock AND total_qty > 0 ";
            } else if ($status === 'HABIS') {
                $sql .= " HAVING total_qty = 0 ";
            }
        }

        $sql .= ") as subquery";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total_rows'];
    }

    public function getDetailLokasi($barangId) {
        $sql = "SELECT l.kode, l.nama, s.qty 
                FROM stok s
                JOIN lokasi l ON s.lokasi_id = l.id
                WHERE s.barang_id = :barang_id AND s.qty > 0
                ORDER BY l.kode ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':barang_id', $barangId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
