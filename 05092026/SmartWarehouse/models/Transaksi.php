<?php

class Transaksi {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function generateTransactionNumber($type) {
        $typeUpper = strtoupper($type);
        if ($typeUpper === 'IN') {
            $prefix = 'TRX-IN-';
        } elseif ($typeUpper === 'OUT') {
            $prefix = 'TRX-OUT-';
        } else {
            $prefix = 'TRF-';
        }
        $dateStr = date('Ymd');
        $prefix .= $dateStr . '-';

        $query = "SELECT transaction_number FROM transaksi 
                  WHERE transaction_number LIKE :prefix 
                  ORDER BY id DESC LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $searchPrefix = $prefix . '%';
        $stmt->bindParam(':prefix', $searchPrefix);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $lastNumber = $row['transaction_number'];
            $lastSequence = intval(substr($lastNumber, -4));
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        return $prefix . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }

    public function createTransaksiIn($dataTransaksi, $dataDetail) {
        try {
            $this->db->beginTransaction();

            // 1. Insert Transaksi
            $queryTransaksi = "INSERT INTO transaksi (transaction_number, type, user_id, supplier_name, transaction_date, notes) 
                               VALUES (:transaction_number, 'in', :user_id, :supplier_name, :transaction_date, :notes)";
            $stmtTransaksi = $this->db->prepare($queryTransaksi);
            $stmtTransaksi->bindParam(':transaction_number', $dataTransaksi['transaction_number']);
            $stmtTransaksi->bindParam(':user_id', $dataTransaksi['user_id']);
            $stmtTransaksi->bindParam(':supplier_name', $dataTransaksi['supplier_name']);
            $stmtTransaksi->bindParam(':transaction_date', $dataTransaksi['transaction_date']);
            $stmtTransaksi->bindParam(':notes', $dataTransaksi['notes']);
            $stmtTransaksi->execute();

            $transaksiId = $this->db->lastInsertId();

            // 2. Insert Transaksi Detail
            $queryDetail = "INSERT INTO transaksi_detail (transaksi_id, barang_id, lokasi_id, qty) 
                            VALUES (:transaksi_id, :barang_id, :lokasi_id, :qty)";
            $stmtDetail = $this->db->prepare($queryDetail);
            $stmtDetail->bindParam(':transaksi_id', $transaksiId);
            $stmtDetail->bindParam(':barang_id', $dataDetail['barang_id']);
            $stmtDetail->bindParam(':lokasi_id', $dataDetail['lokasi_id']);
            $stmtDetail->bindParam(':qty', $dataDetail['qty']);
            $stmtDetail->execute();

            // 3. Update or Insert Stok
            $this->updateStok($dataDetail['barang_id'], $dataDetail['lokasi_id'], $dataDetail['qty'], 'in');

            // 4. Update Lokasi Status
            $this->updateLokasiStatus($dataDetail['lokasi_id']);

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            // Optional: Log error
            return false;
        }
    }

    private function updateStok($barangId, $lokasiId, $qty, $type) {
        // Check if stock exists
        $queryCheck = "SELECT id, qty FROM stok WHERE barang_id = :barang_id AND lokasi_id = :lokasi_id";
        $stmtCheck = $this->db->prepare($queryCheck);
        $stmtCheck->bindParam(':barang_id', $barangId);
        $stmtCheck->bindParam(':lokasi_id', $lokasiId);
        $stmtCheck->execute();

        if ($stmtCheck->rowCount() > 0) {
            // Update existing stock
            $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            $newQty = $type === 'in' ? $row['qty'] + $qty : $row['qty'] - $qty;
            
            $queryUpdate = "UPDATE stok SET qty = :qty WHERE id = :id";
            $stmtUpdate = $this->db->prepare($queryUpdate);
            $stmtUpdate->bindParam(':qty', $newQty);
            $stmtUpdate->bindParam(':id', $row['id']);
            $stmtUpdate->execute();
        } else {
            // Insert new stock (only makes sense for 'in')
            if ($type === 'in') {
                $queryInsert = "INSERT INTO stok (barang_id, lokasi_id, qty) VALUES (:barang_id, :lokasi_id, :qty)";
                $stmtInsert = $this->db->prepare($queryInsert);
                $stmtInsert->bindParam(':barang_id', $barangId);
                $stmtInsert->bindParam(':lokasi_id', $lokasiId);
                $stmtInsert->bindParam(':qty', $qty);
                $stmtInsert->execute();
            } else {
                throw new Exception("Cannot decrement non-existent stock.");
            }
        }
    }

    private function updateLokasiStatus($lokasiId) {
        // Recalculate if location has any stock
        $queryCheck = "SELECT SUM(qty) as total_qty FROM stok WHERE lokasi_id = :lokasi_id";
        $stmtCheck = $this->db->prepare($queryCheck);
        $stmtCheck->bindParam(':lokasi_id', $lokasiId);
        $stmtCheck->execute();
        $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        
        $status = ($result['total_qty'] > 0) ? 'terisi' : 'kosong';

        $queryUpdate = "UPDATE lokasi SET status = :status WHERE id = :lokasi_id AND status != 'tidak_aktif'";
        $stmtUpdate = $this->db->prepare($queryUpdate);
        $stmtUpdate->bindParam(':status', $status);
        $stmtUpdate->bindParam(':lokasi_id', $lokasiId);
        $stmtUpdate->execute();
    }
    public function createTransaksiOut($dataTransaksi, $dataDetail) {
        try {
            $this->db->beginTransaction();

            // Check stock first before doing anything
            $queryCheckStock = "SELECT qty FROM stok WHERE barang_id = :barang_id AND lokasi_id = :lokasi_id";
            $stmtCheckStock = $this->db->prepare($queryCheckStock);
            $stmtCheckStock->bindParam(':barang_id', $dataDetail['barang_id']);
            $stmtCheckStock->bindParam(':lokasi_id', $dataDetail['lokasi_id']);
            $stmtCheckStock->execute();
            
            if ($stmtCheckStock->rowCount() == 0) {
                throw new Exception("Stok barang tidak ditemukan di lokasi ini.");
            }
            $currentStock = $stmtCheckStock->fetch(PDO::FETCH_ASSOC)['qty'];
            if ($currentStock < $dataDetail['qty']) {
                throw new Exception("Jumlah stok tidak mencukupi. Sisa stok: " . $currentStock);
            }

            // 1. Insert Transaksi
            $queryTransaksi = "INSERT INTO transaksi (transaction_number, type, user_id, recipient_name, transaction_date, notes) 
                               VALUES (:transaction_number, 'out', :user_id, :recipient_name, :transaction_date, :notes)";
            $stmtTransaksi = $this->db->prepare($queryTransaksi);
            $stmtTransaksi->bindParam(':transaction_number', $dataTransaksi['transaction_number']);
            $stmtTransaksi->bindParam(':user_id', $dataTransaksi['user_id']);
            $stmtTransaksi->bindParam(':recipient_name', $dataTransaksi['recipient_name']);
            $stmtTransaksi->bindParam(':transaction_date', $dataTransaksi['transaction_date']);
            $stmtTransaksi->bindParam(':notes', $dataTransaksi['notes']);
            $stmtTransaksi->execute();

            $transaksiId = $this->db->lastInsertId();

            // 2. Insert Transaksi Detail
            $queryDetail = "INSERT INTO transaksi_detail (transaksi_id, barang_id, lokasi_id, qty) 
                            VALUES (:transaksi_id, :barang_id, :lokasi_id, :qty)";
            $stmtDetail = $this->db->prepare($queryDetail);
            $stmtDetail->bindParam(':transaksi_id', $transaksiId);
            $stmtDetail->bindParam(':barang_id', $dataDetail['barang_id']);
            $stmtDetail->bindParam(':lokasi_id', $dataDetail['lokasi_id']);
            $stmtDetail->bindParam(':qty', $dataDetail['qty']);
            $stmtDetail->execute();

            // 3. Update Stok (decrement)
            $this->updateStok($dataDetail['barang_id'], $dataDetail['lokasi_id'], $dataDetail['qty'], 'out');

            // 4. Update Lokasi Status
            $this->updateLokasiStatus($dataDetail['lokasi_id']);

            $this->db->commit();
            return ['success' => true];

        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function createTransaksiTransfer($dataTransaksi, $dataDetail) {
        try {
            $this->db->beginTransaction();

            if ($dataDetail['lokasi_id'] == $dataDetail['lokasi_tujuan_id']) {
                throw new Exception("Lokasi asal dan lokasi tujuan tidak boleh sama.");
            }

            // Check stock first before doing anything
            $queryCheckStock = "SELECT qty FROM stok WHERE barang_id = :barang_id AND lokasi_id = :lokasi_id";
            $stmtCheckStock = $this->db->prepare($queryCheckStock);
            $stmtCheckStock->bindParam(':barang_id', $dataDetail['barang_id']);
            $stmtCheckStock->bindParam(':lokasi_id', $dataDetail['lokasi_id']);
            $stmtCheckStock->execute();
            
            if ($stmtCheckStock->rowCount() == 0) {
                throw new Exception("Stok barang tidak ditemukan di lokasi asal.");
            }
            $currentStock = $stmtCheckStock->fetch(PDO::FETCH_ASSOC)['qty'];
            if ($currentStock < $dataDetail['qty']) {
                throw new Exception("Jumlah stok tidak mencukupi. Sisa stok: " . $currentStock);
            }

            // 1. Insert Transaksi (type = 'transfer')
            $queryTransaksi = "INSERT INTO transaksi (transaction_number, type, user_id, transaction_date, notes) 
                               VALUES (:transaction_number, 'transfer', :user_id, :transaction_date, :notes)";
            $stmtTransaksi = $this->db->prepare($queryTransaksi);
            $stmtTransaksi->bindParam(':transaction_number', $dataTransaksi['transaction_number']);
            $stmtTransaksi->bindParam(':user_id', $dataTransaksi['user_id']);
            $stmtTransaksi->bindParam(':transaction_date', $dataTransaksi['transaction_date']);
            $stmtTransaksi->bindParam(':notes', $dataTransaksi['notes']);
            $stmtTransaksi->execute();

            $transaksiId = $this->db->lastInsertId();

            // 2. Insert Transaksi Detail (with lokasi_tujuan_id)
            $queryDetail = "INSERT INTO transaksi_detail (transaksi_id, barang_id, lokasi_id, lokasi_tujuan_id, qty) 
                            VALUES (:transaksi_id, :barang_id, :lokasi_id, :lokasi_tujuan_id, :qty)";
            $stmtDetail = $this->db->prepare($queryDetail);
            $stmtDetail->bindParam(':transaksi_id', $transaksiId);
            $stmtDetail->bindParam(':barang_id', $dataDetail['barang_id']);
            $stmtDetail->bindParam(':lokasi_id', $dataDetail['lokasi_id']);
            $stmtDetail->bindParam(':lokasi_tujuan_id', $dataDetail['lokasi_tujuan_id']);
            $stmtDetail->bindParam(':qty', $dataDetail['qty']);
            $stmtDetail->execute();

            // 3. Update Stok (decrement asal, increment tujuan)
            $this->updateStok($dataDetail['barang_id'], $dataDetail['lokasi_id'], $dataDetail['qty'], 'out');
            $this->updateStok($dataDetail['barang_id'], $dataDetail['lokasi_tujuan_id'], $dataDetail['qty'], 'in');

            // 4. Update Lokasi Status
            $this->updateLokasiStatus($dataDetail['lokasi_id']);
            $this->updateLokasiStatus($dataDetail['lokasi_tujuan_id']);

            $this->db->commit();
            return ['success' => true];

        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // =========================================
    //  RIWAYAT TRANSAKSI - Query & Detail
    // =========================================
    public function getRiwayat($filters = [], $limit = 15, $offset = 0) {
        $params = [];
        $where  = [];

        $sql = "SELECT 
                    t.id,
                    t.transaction_number,
                    t.type,
                    t.transaction_date,
                    t.notes,
                    t.supplier_name,
                    t.recipient_name,
                    u.full_name AS operator,
                    b.code     AS barang_code,
                    b.name     AS barang_name,
                    b.unit     AS barang_unit,
                    td.qty,
                    l_asal.kode   AS lokasi_asal_kode,
                    l_asal.nama   AS lokasi_asal_nama,
                    l_tujuan.kode AS lokasi_tujuan_kode,
                    l_tujuan.nama AS lokasi_tujuan_nama
                FROM transaksi t
                JOIN users u        ON t.user_id   = u.id
                JOIN transaksi_detail td ON td.transaksi_id = t.id
                JOIN barang b       ON td.barang_id = b.id
                JOIN lokasi l_asal  ON td.lokasi_id = l_asal.id
                LEFT JOIN lokasi l_tujuan ON td.lokasi_tujuan_id = l_tujuan.id ";

        if (!empty($filters['search'])) {
            $where[] = "(t.transaction_number LIKE :search OR b.name LIKE :search OR b.code LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['type'])) {
            $where[] = "t.type = :type";
            $params[':type'] = $filters['type'];
        }
        if (!empty($filters['barang_id'])) {
            $where[] = "b.id = :barang_id";
            $params[':barang_id'] = $filters['barang_id'];
        }
        if (!empty($filters['user_id'])) {
            $where[] = "t.user_id = :user_id";
            $params[':user_id'] = $filters['user_id'];
        }
        if (!empty($filters['date_from'])) {
            $where[] = "DATE(t.transaction_date) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[] = "DATE(t.transaction_date) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY t.transaction_date DESC, t.id DESC";
        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':limit',  (int)$limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countRiwayat($filters = []) {
        $params = [];
        $where  = [];

        $sql = "SELECT COUNT(*) AS total
                FROM transaksi t
                JOIN users u        ON t.user_id   = u.id
                JOIN transaksi_detail td ON td.transaksi_id = t.id
                JOIN barang b       ON td.barang_id = b.id
                JOIN lokasi l_asal  ON td.lokasi_id = l_asal.id
                LEFT JOIN lokasi l_tujuan ON td.lokasi_tujuan_id = l_tujuan.id ";

        if (!empty($filters['search'])) {
            $where[] = "(t.transaction_number LIKE :search OR b.name LIKE :search OR b.code LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['type'])) {
            $where[] = "t.type = :type";
            $params[':type'] = $filters['type'];
        }
        if (!empty($filters['barang_id'])) {
            $where[] = "b.id = :barang_id";
            $params[':barang_id'] = $filters['barang_id'];
        }
        if (!empty($filters['user_id'])) {
            $where[] = "t.user_id = :user_id";
            $params[':user_id'] = $filters['user_id'];
        }
        if (!empty($filters['date_from'])) {
            $where[] = "DATE(t.transaction_date) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[] = "DATE(t.transaction_date) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->execute();
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getDetailById($transaksiId) {
        $sql = "SELECT 
                    t.transaction_number, t.type, t.transaction_date, t.notes,
                    t.supplier_name, t.recipient_name,
                    u.full_name AS operator, u.username,
                    b.code AS barang_code, b.name AS barang_name, b.unit AS barang_unit,
                    td.qty,
                    l_asal.kode  AS lokasi_asal_kode,  l_asal.nama  AS lokasi_asal_nama,
                    l_tujuan.kode AS lokasi_tujuan_kode, l_tujuan.nama AS lokasi_tujuan_nama
                FROM transaksi t
                JOIN users u        ON t.user_id = u.id
                JOIN transaksi_detail td ON td.transaksi_id = t.id
                JOIN barang b       ON td.barang_id = b.id
                JOIN lokasi l_asal  ON td.lokasi_id = l_asal.id
                LEFT JOIN lokasi l_tujuan ON td.lokasi_tujuan_id = l_tujuan.id
                WHERE t.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', (int)$transaksiId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
