<?php
require_once '../helpers/AuthHelper.php';
require_once '../models/Transaksi.php';
require_once '../models/Barang.php';
require_once '../models/User.php';

class RiwayatController {

    public function __construct() {
        AuthHelper::requireRole(['Admin', 'Supervisor']);
    }

    public function index() {
        $transaksiModel = new Transaksi();
        $barangModel    = new Barang();
        $userModel      = new User();

        // Collect filters
        $filters = [
            'search'    => trim($_GET['search'] ?? ''),
            'type'      => $_GET['type'] ?? '',
            'barang_id' => $_GET['barang_id'] ?? '',
            'user_id'   => $_GET['user_id'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to'   => $_GET['date_to'] ?? '',
        ];

        // Pagination
        $limit      = 15;
        $page       = max(1, (int)($_GET['page'] ?? 1));
        $offset     = ($page - 1) * $limit;

        $riwayat    = $transaksiModel->getRiwayat($filters, $limit, $offset);
        $totalRows  = $transaksiModel->countRiwayat($filters);
        $totalPages = ceil($totalRows / $limit);

        // Filter dropdown data
        $barangList = $barangModel->getAll();
        $userList   = $userModel->getAll();

        $title = "Riwayat Transaksi - " . APP_NAME;
        require_once '../views/layouts/header.php';
        require_once '../views/layouts/sidebar.php';
        require_once '../views/riwayat/index.php';
        require_once '../views/layouts/footer.php';
    }

    // AJAX – detail 1 transaksi
    public function detail() {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false]);
            exit;
        }
        $transaksiModel = new Transaksi();
        $data = $transaksiModel->getDetailById($id);
        header('Content-Type: application/json');
        echo json_encode(['success' => (bool)$data, 'data' => $data]);
        exit;
    }
}
