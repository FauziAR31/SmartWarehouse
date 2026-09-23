<?php
require_once __DIR__ . '/../config/database.php';

class Laporan {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // =====================================================
    // Helper: build common WHERE from filters
    // =====================================================
    private function buildWhere($filters, &$params, $tableAlias = 't') {
        $where = [];
        if (!empty($filters['date_from'])) {
            $where[] = "DATE({$tableAlias}.transaction_date) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[] = "DATE({$tableAlias}.transaction_date) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }
        if (!empty($filters['barang_id'])) {
            $where[] = "b.id = :barang_id";
            $params[':barang_id'] = $filters['barang_id'];
        }
        if (!empty($filters['kategori_id'])) {
            $where[] = "b.kategori_id = :kategori_id";
            $params[':kategori_id'] = $filters['kategori_id'];
        }
        if (!empty($filters['lokasi_id'])) {
            $where[] = "td.lokasi_id = :lokasi_id";
            $params[':lokasi_id'] = $filters['lokasi_id'];
        }
        if (!empty($filters['user_id'])) {
            $where[] = "{$tableAlias}.user_id = :user_id";
            $params[':user_id'] = $filters['user_id'];
        }
        return $where;
    }

    private function execFiltered($sql, $params) {
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =====================================================
    // 1. Laporan Stok (current stock snapshot)
    // =====================================================
    public function getLaporanStok($filters = []) {
        $params = [];
        $where  = ["1=1"];
        if (!empty($filters['barang_id']))   { $where[] = "b.id = :barang_id";         $params[':barang_id']   = $filters['barang_id']; }
        if (!empty($filters['kategori_id'])) { $where[] = "b.kategori_id = :kat_id";   $params[':kat_id']      = $filters['kategori_id']; }
        if (!empty($filters['lokasi_id']))   { $where[] = "s.lokasi_id = :lokasi_id";  $params[':lokasi_id']   = $filters['lokasi_id']; }

        $statusHaving = '';
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'AMAN')     $statusHaving = "HAVING total_qty > b.min_stock";
            elseif ($filters['status'] === 'MINIMUM') $statusHaving = "HAVING total_qty <= b.min_stock AND total_qty > 0";
            elseif ($filters['status'] === 'HABIS')   $statusHaving = "HAVING total_qty = 0";
        }

        $sql = "SELECT b.code, b.name AS barang_name, k.name AS kategori_name,
                       b.unit, b.min_stock,
                       COALESCE(SUM(s.qty), 0) AS total_qty,
                       GROUP_CONCAT(DISTINCT l.kode ORDER BY l.kode SEPARATOR ', ') AS lokasi_list
                FROM barang b
                LEFT JOIN kategori k ON b.kategori_id = k.id
                LEFT JOIN stok s    ON b.id = s.barang_id
                LEFT JOIN lokasi l  ON s.lokasi_id = l.id
                WHERE " . implode(' AND ', $where) . "
                GROUP BY b.id, b.code, b.name, k.name, b.unit, b.min_stock
                $statusHaving
                ORDER BY b.code ASC";

        return $this->execFiltered($sql, $params);
    }

    // =====================================================
    // 2. Laporan Barang Masuk
    // =====================================================
    public function getLaporanMasuk($filters = []) {
        $params = [];
        $where  = ["t.type = 'in'"];
        $extra  = $this->buildWhere($filters, $params);
        $where  = array_merge($where, $extra);

        $sql = "SELECT t.transaction_number, t.transaction_date,
                       b.code AS barang_code, b.name AS barang_name, b.unit,
                       k.name AS kategori_name, td.qty,
                       l.kode AS lokasi_kode, l.nama AS lokasi_nama,
                       t.supplier_name, t.notes,
                       u.full_name AS operator
                FROM transaksi t
                JOIN transaksi_detail td ON td.transaksi_id = t.id
                JOIN barang b   ON td.barang_id = b.id
                JOIN kategori k ON b.kategori_id = k.id
                JOIN lokasi l   ON td.lokasi_id = l.id
                JOIN users u    ON t.user_id = u.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY t.transaction_date DESC";

        return $this->execFiltered($sql, $params);
    }

    // =====================================================
    // 3. Laporan Barang Keluar
    // =====================================================
    public function getLaporanKeluar($filters = []) {
        $params = [];
        $where  = ["t.type = 'out'"];
        $extra  = $this->buildWhere($filters, $params);
        $where  = array_merge($where, $extra);

        $sql = "SELECT t.transaction_number, t.transaction_date,
                       b.code AS barang_code, b.name AS barang_name, b.unit,
                       k.name AS kategori_name, td.qty,
                       l.kode AS lokasi_kode, l.nama AS lokasi_nama,
                       t.recipient_name, t.notes,
                       u.full_name AS operator
                FROM transaksi t
                JOIN transaksi_detail td ON td.transaksi_id = t.id
                JOIN barang b   ON td.barang_id = b.id
                JOIN kategori k ON b.kategori_id = k.id
                JOIN lokasi l   ON td.lokasi_id = l.id
                JOIN users u    ON t.user_id = u.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY t.transaction_date DESC";

        return $this->execFiltered($sql, $params);
    }

    // =====================================================
    // 4. Laporan Pemindahan
    // =====================================================
    public function getLaporanPemindahan($filters = []) {
        $params = [];
        $where  = ["t.type = 'transfer'"];

        if (!empty($filters['date_from'])) { $where[] = "DATE(t.transaction_date) >= :date_from"; $params[':date_from'] = $filters['date_from']; }
        if (!empty($filters['date_to']))   { $where[] = "DATE(t.transaction_date) <= :date_to";   $params[':date_to']   = $filters['date_to']; }
        if (!empty($filters['barang_id']))   { $where[] = "b.id = :barang_id";         $params[':barang_id']   = $filters['barang_id']; }
        if (!empty($filters['kategori_id'])) { $where[] = "b.kategori_id = :kat_id";   $params[':kat_id']      = $filters['kategori_id']; }
        if (!empty($filters['user_id']))     { $where[] = "t.user_id = :user_id";       $params[':user_id']     = $filters['user_id']; }
        if (!empty($filters['lokasi_id'])) {
            $where[] = "(td.lokasi_id = :lokasi_id OR td.lokasi_tujuan_id = :lokasi_id2)";
            $params[':lokasi_id']  = $filters['lokasi_id'];
            $params[':lokasi_id2'] = $filters['lokasi_id'];
        }

        $sql = "SELECT t.transaction_number, t.transaction_date,
                       b.code AS barang_code, b.name AS barang_name, b.unit,
                       k.name AS kategori_name, td.qty,
                       l_asal.kode AS lokasi_asal_kode, l_asal.nama AS lokasi_asal_nama,
                       l_tujuan.kode AS lokasi_tujuan_kode, l_tujuan.nama AS lokasi_tujuan_nama,
                       t.notes, u.full_name AS operator
                FROM transaksi t
                JOIN transaksi_detail td ON td.transaksi_id = t.id
                JOIN barang b   ON td.barang_id = b.id
                JOIN kategori k ON b.kategori_id = k.id
                JOIN lokasi l_asal  ON td.lokasi_id = l_asal.id
                LEFT JOIN lokasi l_tujuan ON td.lokasi_tujuan_id = l_tujuan.id
                JOIN users u    ON t.user_id = u.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY t.transaction_date DESC";

        return $this->execFiltered($sql, $params);
    }

    // =====================================================
    // 5. Laporan Aktivitas Gudang (log_aktivitas)
    // =====================================================
    public function getLaporanAktivitas($filters = []) {
        $params = [];
        $where  = ["1=1"];

        if (!empty($filters['date_from'])) { $where[] = "DATE(la.created_at) >= :date_from"; $params[':date_from'] = $filters['date_from']; }
        if (!empty($filters['date_to']))   { $where[] = "DATE(la.created_at) <= :date_to";   $params[':date_to']   = $filters['date_to']; }
        if (!empty($filters['user_id']))   { $where[] = "la.user_id = :user_id";             $params[':user_id']   = $filters['user_id']; }
        if (!empty($filters['action']))    { $where[] = "la.action LIKE :action";             $params[':action']    = '%' . $filters['action'] . '%'; }

        $sql = "SELECT la.id, la.action, la.module, la.description, la.related_data, la.ip_address,
                       la.created_at, u.full_name AS operator, u.username
                FROM log_aktivitas la
                JOIN users u ON la.user_id = u.id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY la.created_at DESC";

        return $this->execFiltered($sql, $params);
    }
}
