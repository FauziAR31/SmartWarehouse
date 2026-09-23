<?php
require_once '../helpers/AuthHelper.php';
require_once '../models/Stok.php';
require_once '../models/Kategori.php';
require_once '../models/Lokasi.php';

class StokController {

    public function __construct() {
        AuthHelper::requireRole(['Admin', 'Supervisor']);
    }

    public function index() {
        $stokModel    = new Stok();
        $kategoriModel = new Kategori();
        $lokasiModel  = new Lokasi();

        $search      = trim($_GET['search'] ?? '');
        $kategori_id = $_GET['kategori_id'] ?? '';
        $lokasi_id   = $_GET['lokasi_id'] ?? '';
        $status      = $_GET['status'] ?? '';

        $limit  = 10;
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * $limit;

        $stockData   = $stokModel->getStockReport($search, $kategori_id, $lokasi_id, $status, $limit, $offset);
        $totalRows   = $stokModel->countStockReport($search, $kategori_id, $lokasi_id, $status);
        $totalPages  = ceil($totalRows / $limit);

        $kategoriList = $kategoriModel->getAll();
        $lokasiList   = $lokasiModel->getAll();

        $title = "Stok Barang - " . APP_NAME;
        require_once '../views/layouts/header.php';
        require_once '../views/layouts/sidebar.php';
        require_once '../views/stok/index.php';
        require_once '../views/layouts/footer.php';
    }

    // AJAX API - detail stok per lokasi untuk 1 barang
    public function apiDetailLokasi() {
        header('Content-Type: application/json');
        $barangId = (int)($_GET['barang_id'] ?? 0);
        if ($barangId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Barang ID tidak valid']);
            exit;
        }
        $stokModel = new Stok();
        $detail    = $stokModel->getDetailLokasi($barangId);
        echo json_encode(['success' => true, 'data' => $detail]);
        exit;
    }
}
