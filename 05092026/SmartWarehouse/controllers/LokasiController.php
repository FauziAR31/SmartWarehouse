<?php
require_once '../helpers/AuthHelper.php';
require_once '../models/Lokasi.php';

class LokasiController {

    public function __construct() {
        AuthHelper::requireRole(['Admin', 'Supervisor']);
    }

    // =============================================
    //  INDEX - List with search, filter, pagination
    // =============================================
    public function index() {
        $lokasiModel = new Lokasi();

        $search     = trim($_GET['search'] ?? '');
        $rackFilter = trim($_GET['rack'] ?? '');

        $limit      = 15;
        $page       = max(1, (int)($_GET['page'] ?? 1));
        $offset     = ($page - 1) * $limit;

        $totalRows  = $lokasiModel->getTotalRows($search, $rackFilter);
        $totalPages = ceil($totalRows / $limit);
        $items      = $lokasiModel->getAllPaginated($limit, $offset, $search, $rackFilter);
        $racks      = $lokasiModel->getDistinctRacks();
        $stats      = $lokasiModel->getStats();

        // Fetch items per location (barang)
        $itemsByLokasi = [];
        foreach ($items as $lokasi) {
            $itemsByLokasi[$lokasi['id']] = $lokasiModel->getItemsByLokasiId($lokasi['id']);
        }

        $title = "Master Data Lokasi - " . APP_NAME;
        require_once '../views/layouts/header.php';
        require_once '../views/layouts/sidebar.php';
        require_once '../views/lokasi/index.php';
        require_once '../views/layouts/footer.php';
    }

    // =============================================
    //  CREATE form
    // =============================================
    public function create() {
        $lokasiModel = new Lokasi();
        $racks       = $lokasiModel->getDistinctRacks();

        $title = "Tambah Lokasi - " . APP_NAME;
        require_once '../views/layouts/header.php';
        require_once '../views/layouts/sidebar.php';
        require_once '../views/lokasi/form.php';
        require_once '../views/layouts/footer.php';
    }

    // =============================================
    //  STORE
    // =============================================
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/lokasi/index");
            exit;
        }

        $rack   = strtoupper(trim($_POST['rack'] ?? ''));
        $level  = (int)($_POST['level'] ?? 0);
        $posisi = (int)($_POST['posisi'] ?? 0);
        $kode   = Lokasi::generateKode($rack, $level, $posisi);

        $data = [
            'kode'      => $kode,
            'rack'      => $rack,
            'level'     => $level,
            'posisi'    => $posisi,
            'nama'      => trim($_POST['nama'] ?? ''),
            'deskripsi' => trim($_POST['deskripsi'] ?? ''),
            'kapasitas' => max(0, (int)($_POST['kapasitas'] ?? 0)),
            'status'    => $_POST['status'] ?? 'kosong',
        ];

        // Validation
        if (empty($rack) || $level < 1 || $posisi < 1 || empty($data['nama'])) {
            $_SESSION['error'] = "Rack, Level, Posisi, dan Nama Lokasi wajib diisi!";
            header("Location: " . BASE_URL . "/lokasi/create");
            exit;
        }

        if (!preg_match('/^[A-Z]$/', $rack)) {
            $_SESSION['error'] = "Rack hanya boleh satu huruf kapital (contoh: A, B, C).";
            header("Location: " . BASE_URL . "/lokasi/create");
            exit;
        }

        $lokasiModel = new Lokasi();
        if (!$lokasiModel->isKodeUnique($kode)) {
            $_SESSION['error'] = "Kode lokasi <strong>$kode</strong> sudah digunakan. Gunakan kombinasi Rack, Level, dan Posisi yang berbeda.";
            header("Location: " . BASE_URL . "/lokasi/create");
            exit;
        }

        if ($lokasiModel->create($data)) {
            $_SESSION['success'] = "Lokasi <strong>$kode</strong> berhasil ditambahkan.";
            header("Location: " . BASE_URL . "/lokasi/index");
        } else {
            $_SESSION['error'] = "Gagal menambahkan lokasi. Coba lagi.";
            header("Location: " . BASE_URL . "/lokasi/create");
        }
        exit;
    }

    // =============================================
    //  EDIT form
    // =============================================
    public function edit($id = null) {
        if (!$id) {
            header("Location: " . BASE_URL . "/lokasi/index");
            exit;
        }

        $lokasiModel = new Lokasi();
        $lokasi      = $lokasiModel->getById($id);
        $racks       = $lokasiModel->getDistinctRacks();

        if (!$lokasi) {
            $_SESSION['error'] = "Lokasi tidak ditemukan.";
            header("Location: " . BASE_URL . "/lokasi/index");
            exit;
        }

        $title = "Edit Lokasi - " . APP_NAME;
        require_once '../views/layouts/header.php';
        require_once '../views/layouts/sidebar.php';
        require_once '../views/lokasi/form.php';
        require_once '../views/layouts/footer.php';
    }

    // =============================================
    //  UPDATE
    // =============================================
    public function update($id = null) {
        if (!$id) {
            header("Location: " . BASE_URL . "/lokasi/index");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/lokasi/index");
            exit;
        }

        $rack   = strtoupper(trim($_POST['rack'] ?? ''));
        $level  = (int)($_POST['level'] ?? 0);
        $posisi = (int)($_POST['posisi'] ?? 0);
        $kode   = Lokasi::generateKode($rack, $level, $posisi);

        $data = [
            'kode'      => $kode,
            'rack'      => $rack,
            'level'     => $level,
            'posisi'    => $posisi,
            'nama'      => trim($_POST['nama'] ?? ''),
            'deskripsi' => trim($_POST['deskripsi'] ?? ''),
            'kapasitas' => max(0, (int)($_POST['kapasitas'] ?? 0)),
            'status'    => $_POST['status'] ?? 'kosong',
        ];

        if (empty($rack) || $level < 1 || $posisi < 1 || empty($data['nama'])) {
            $_SESSION['error'] = "Rack, Level, Posisi, dan Nama Lokasi wajib diisi!";
            header("Location: " . BASE_URL . "/lokasi/edit/$id");
            exit;
        }

        if (!preg_match('/^[A-Z]$/', $rack)) {
            $_SESSION['error'] = "Rack hanya boleh satu huruf kapital (contoh: A, B, C).";
            header("Location: " . BASE_URL . "/lokasi/edit/$id");
            exit;
        }

        $lokasiModel = new Lokasi();
        if (!$lokasiModel->isKodeUnique($kode, $id)) {
            $_SESSION['error'] = "Kode lokasi <strong>$kode</strong> sudah digunakan oleh lokasi lain.";
            header("Location: " . BASE_URL . "/lokasi/edit/$id");
            exit;
        }

        if ($lokasiModel->update($id, $data)) {
            $_SESSION['success'] = "Lokasi <strong>$kode</strong> berhasil diperbarui.";
            header("Location: " . BASE_URL . "/lokasi/index");
        } else {
            $_SESSION['error'] = "Gagal memperbarui lokasi.";
            header("Location: " . BASE_URL . "/lokasi/edit/$id");
        }
        exit;
    }

    // =============================================
    //  DELETE
    // =============================================
    public function delete($id = null) {
        if ($id) {
            $lokasiModel = new Lokasi();
            if ($lokasiModel->delete($id)) {
                $_SESSION['success'] = "Lokasi berhasil dihapus.";
            } else {
                $_SESSION['error'] = "Gagal menghapus lokasi! Lokasi ini masih memiliki stok barang. Kosongkan stok terlebih dahulu.";
            }
        }
        header("Location: " . BASE_URL . "/lokasi/index");
        exit;
    }
}
