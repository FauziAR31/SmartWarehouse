<?php

class Dashboard {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getTotalBarang() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM barang");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getTotalStok() {
        $stmt = $this->db->query("SELECT SUM(qty) as total FROM stok");
        $res = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        return $res ? $res : 0;
    }

    public function getBarangMasukHariIni() {
        $query = "SELECT SUM(td.qty) as total 
                  FROM transaksi_detail td 
                  JOIN transaksi t ON td.transaksi_id = t.id 
                  WHERE t.type = 'in' AND DATE(t.transaction_date) = CURDATE()";
        $stmt = $this->db->query($query);
        $res = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        return $res ? $res : 0;
    }

    public function getBarangKeluarHariIni() {
        $query = "SELECT SUM(td.qty) as total 
                  FROM transaksi_detail td 
                  JOIN transaksi t ON td.transaksi_id = t.id 
                  WHERE t.type = 'out' AND DATE(t.transaction_date) = CURDATE()";
        $stmt = $this->db->query($query);
        $res = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        return $res ? $res : 0;
    }

    public function getTotalLokasi() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM lokasi");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getBarangStokMinimum($min = 10) {
        $query = "SELECT b.id, b.code, b.name, COALESCE(SUM(s.qty), 0) as total_qty 
                  FROM barang b 
                  LEFT JOIN stok s ON b.id = s.barang_id 
                  GROUP BY b.id 
                  HAVING total_qty > 0 AND total_qty <= :min
                  ORDER BY total_qty ASC LIMIT 5";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':min', $min, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBarangHabis() {
        $query = "SELECT b.id, b.code, b.name, COALESCE(SUM(s.qty), 0) as total_qty 
                  FROM barang b 
                  LEFT JOIN stok s ON b.id = s.barang_id 
                  GROUP BY b.id 
                  HAVING total_qty = 0
                  ORDER BY b.name ASC LIMIT 5";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentTransactions($limit = 5) {
        $query = "SELECT t.transaction_number, t.type, t.transaction_date, u.full_name as user_name
                  FROM transaksi t
                  JOIN users u ON t.user_id = u.id
                  ORDER BY t.transaction_date DESC LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getChartData() {
        // Get last 7 days of data for IN and OUT
        $data = [
            'labels' => [],
            'in' => [],
            'out' => []
        ];

        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $data['labels'][] = date('d M', strtotime($date));

            // In
            $stmtIn = $this->db->prepare("SELECT SUM(td.qty) as total FROM transaksi_detail td JOIN transaksi t ON td.transaksi_id = t.id WHERE t.type = 'in' AND DATE(t.transaction_date) = :d");
            $stmtIn->execute(['d' => $date]);
            $inTotal = $stmtIn->fetch(PDO::FETCH_ASSOC)['total'];
            $data['in'][] = $inTotal ? $inTotal : 0;

            // Out
            $stmtOut = $this->db->prepare("SELECT SUM(td.qty) as total FROM transaksi_detail td JOIN transaksi t ON td.transaksi_id = t.id WHERE t.type = 'out' AND DATE(t.transaction_date) = :d");
            $stmtOut->execute(['d' => $date]);
            $outTotal = $stmtOut->fetch(PDO::FETCH_ASSOC)['total'];
            $data['out'][] = $outTotal ? $outTotal : 0;
        }

        return $data;
    }
}
