<?php
require_once '../helpers/AuthHelper.php';
require_once '../models/Transaksi.php';
require_once '../models/Barang.php';
require_once '../models/Lokasi.php';
require_once '../models/LogAktivitas.php';

class TransaksiController {

    public function __construct() {
        AuthHelper::requireRole(['Admin', 'Supervisor', 'Operator']);
    }

    // =============================================
    //  BARANG MASUK - Form
    // =============================================
    public function masuk() {
        $transaksiModel = new Transaksi();
        $barangModel    = new Barang();
        $lokasiModel    = new Lokasi();

        $nomorTransaksi = $transaksiModel->generateTransactionNumber('IN');
        $tanggal        = date('Y-m-d H:i:s');
        $barangList     = $barangModel->getAllActive();
        $lokasiList     = $lokasiModel->getAll();

        $title = "Barang Masuk - " . APP_NAME;
        require_once '../views/layouts/header.php';
        require_once '../views/layouts/sidebar.php';
        require_once '../views/transaksi/masuk_form.php';
        require_once '../views/layouts/footer.php';
    }

    // =============================================
    //  BARANG MASUK - Store (POST)
    // =============================================
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/transaksi/masuk");
            exit;
        }

        // Collect & sanitize input
        $barangId   = (int)($_POST['barang_id'] ?? 0);
        $lokasiId   = (int)($_POST['lokasi_id'] ?? 0);
        $qty        = (int)($_POST['qty'] ?? 0);
        $supplier   = trim($_POST['supplier_name'] ?? '');
        $keterangan = trim($_POST['keterangan'] ?? '');
        $userId     = $_SESSION['user_id'] ?? null;

        // Validation
        $errors = [];
        if ($barangId <= 0)   $errors[] = "Barang harus dipilih.";
        if ($lokasiId <= 0)   $errors[] = "Lokasi harus dipilih.";
        if ($qty <= 0)        $errors[] = "Jumlah harus lebih dari 0.";
        if (empty($supplier)) $errors[] = "Supplier/asal barang wajib diisi.";

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            header("Location: " . BASE_URL . "/transaksi/masuk");
            exit;
        }

        $transaksiModel = new Transaksi();
        $nomorTransaksi = $transaksiModel->generateTransactionNumber('IN');

        $dataTransaksi = [
            'transaction_number' => $nomorTransaksi,
            'user_id'            => $userId,
            'supplier_name'      => $supplier,
            'transaction_date'   => date('Y-m-d H:i:s'),
            'notes'              => $keterangan,
        ];

        $dataDetail = [
            'barang_id' => $barangId,
            'lokasi_id' => $lokasiId,
            'qty'       => $qty,
        ];

        $result = $transaksiModel->createTransaksiIn($dataTransaksi, $dataDetail);

        if ($result) {
            // Log activity
            $logModel = new LogAktivitas();
            $logModel->record(
                'BARANG_MASUK',
                "Transaksi barang masuk #$nomorTransaksi berhasil. Qty: $qty, Supplier: $supplier",
                'Transaksi',
                $nomorTransaksi
            );

            $_SESSION['success'] = "Transaksi <strong>$nomorTransaksi</strong> berhasil disimpan!";
            header("Location: " . BASE_URL . "/transaksi/masuk");
        } else {
            $_SESSION['error'] = "Transaksi gagal disimpan. Silakan coba lagi.";
            header("Location: " . BASE_URL . "/transaksi/masuk");
        }
        exit;
    }

    // =============================================
    //  API - Get Barang by Code (AJAX)
    // =============================================
    public function apiBarang() {
        header('Content-Type: application/json');

        $code = trim($_GET['code'] ?? '');
        if (empty($code)) {
            echo json_encode(['success' => false, 'message' => 'Kode barang kosong']);
            exit;
        }

        $barangModel = new Barang();
        $barang = $barangModel->getByCode($code);

        if ($barang) {
            echo json_encode([
                'success' => true,
                'data'    => [
                    'id'   => $barang['id'],
                    'name' => $barang['name'],
                    'unit' => $barang['unit'],
                    'code' => $barang['code'],
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Barang tidak ditemukan atau tidak aktif']);
        }
        exit;
    }

    // =============================================
    //  BARANG KELUAR - Form
    // =============================================
    public function keluar() {
        $transaksiModel = new Transaksi();
        $barangModel    = new Barang();

        $nomorTransaksi = $transaksiModel->generateTransactionNumber('OUT');
        $tanggal        = date('Y-m-d H:i:s');
        $barangList     = $barangModel->getAllActive();

        $title = "Barang Keluar - " . APP_NAME;
        require_once '../views/layouts/header.php';
        require_once '../views/layouts/sidebar.php';
        require_once '../views/transaksi/keluar_form.php';
        require_once '../views/layouts/footer.php';
    }

    // =============================================
    //  BARANG KELUAR - Store (POST)
    // =============================================
    public function storeKeluar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/transaksi/keluar");
            exit;
        }

        // Collect & sanitize input
        $barangId   = (int)($_POST['barang_id'] ?? 0);
        $lokasiId   = (int)($_POST['lokasi_id'] ?? 0);
        $qty        = (int)($_POST['qty'] ?? 0);
        $recipient  = trim($_POST['recipient_name'] ?? '');
        $keterangan = trim($_POST['keterangan'] ?? '');
        $userId     = $_SESSION['user_id'] ?? null;

        // Validation
        $errors = [];
        if ($barangId <= 0)   $errors[] = "Barang harus dipilih.";
        if ($lokasiId <= 0)   $errors[] = "Lokasi harus dipilih.";
        if ($qty <= 0)        $errors[] = "Jumlah harus lebih dari 0.";
        if (empty($recipient)) $errors[] = "Tujuan/penerima wajib diisi.";

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            header("Location: " . BASE_URL . "/transaksi/keluar");
            exit;
        }

        $transaksiModel = new Transaksi();
        $nomorTransaksi = $transaksiModel->generateTransactionNumber('OUT');

        $dataTransaksi = [
            'transaction_number' => $nomorTransaksi,
            'user_id'            => $userId,
            'recipient_name'     => $recipient,
            'transaction_date'   => date('Y-m-d H:i:s'),
            'notes'              => $keterangan,
        ];

        $dataDetail = [
            'barang_id' => $barangId,
            'lokasi_id' => $lokasiId,
            'qty'       => $qty,
        ];

        $result = $transaksiModel->createTransaksiOut($dataTransaksi, $dataDetail);

        if ($result['success']) {
            // Log activity
            $logModel = new LogAktivitas();
            $logModel->record(
                'BARANG_KELUAR',
                "Transaksi barang keluar #$nomorTransaksi berhasil. Qty: $qty, Tujuan: $recipient",
                'Transaksi',
                $nomorTransaksi
            );

            $_SESSION['success'] = "Transaksi <strong>$nomorTransaksi</strong> berhasil disimpan!";
            header("Location: " . BASE_URL . "/transaksi/keluar");
        } else {
            $_SESSION['error'] = "Gagal: " . $result['message'];
            header("Location: " . BASE_URL . "/transaksi/keluar");
        }
        exit;
    }

    // =============================================
    //  API - Get Lokasi & Stok by Barang ID (AJAX)
    // =============================================
    public function apiLokasiStok() {
        header('Content-Type: application/json');

        $barangId = (int)($_GET['barang_id'] ?? 0);
        if ($barangId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Barang ID tidak valid']);
            exit;
        }

        $barangModel = new Barang();
        $locations = $barangModel->getAvailableLocations($barangId);

        echo json_encode([
            'success' => true,
            'data'    => $locations
        ]);
        exit;
    }
    // =============================================
    //  PEMINDAHAN BARANG (TRANSFER) - Form
    // =============================================
    public function transfer() {
        $transaksiModel = new Transaksi();
        $barangModel    = new Barang();
        $lokasiModel    = new Lokasi();

        $nomorTransaksi = $transaksiModel->generateTransactionNumber('TRF');
        $tanggal        = date('Y-m-d H:i:s');
        $barangList     = $barangModel->getAllActive();
        $lokasiList     = $lokasiModel->getAll();

        $title = "Pemindahan Barang - " . APP_NAME;
        require_once '../views/layouts/header.php';
        require_once '../views/layouts/sidebar.php';
        require_once '../views/transaksi/transfer_form.php';
        require_once '../views/layouts/footer.php';
    }

    // =============================================
    //  PEMINDAHAN BARANG - Store (POST)
    // =============================================
    public function storeTransfer() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/transaksi/transfer");
            exit;
        }

        // Collect & sanitize input
        $barangId       = (int)($_POST['barang_id'] ?? 0);
        $lokasiId       = (int)($_POST['lokasi_id'] ?? 0);
        $lokasiTujuanId = (int)($_POST['lokasi_tujuan_id'] ?? 0);
        $qty            = (int)($_POST['qty'] ?? 0);
        $keterangan     = trim($_POST['keterangan'] ?? '');
        $userId         = $_SESSION['user_id'] ?? null;

        // Validation
        $errors = [];
        if ($barangId <= 0)   $errors[] = "Barang harus dipilih.";
        if ($lokasiId <= 0)   $errors[] = "Lokasi asal harus dipilih.";
        if ($lokasiTujuanId <= 0) $errors[] = "Lokasi tujuan harus dipilih.";
        if ($qty <= 0)        $errors[] = "Jumlah harus lebih dari 0.";
        if ($lokasiId === $lokasiTujuanId) {
            $errors[] = "Lokasi asal dan tujuan tidak boleh sama.";
        }

        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            header("Location: " . BASE_URL . "/transaksi/transfer");
            exit;
        }

        $transaksiModel = new Transaksi();
        $nomorTransaksi = $transaksiModel->generateTransactionNumber('TRF');

        $dataTransaksi = [
            'transaction_number' => $nomorTransaksi,
            'user_id'            => $userId,
            'transaction_date'   => date('Y-m-d H:i:s'),
            'notes'              => $keterangan,
        ];

        $dataDetail = [
            'barang_id'        => $barangId,
            'lokasi_id'        => $lokasiId,
            'lokasi_tujuan_id' => $lokasiTujuanId,
            'qty'              => $qty,
        ];

        $result = $transaksiModel->createTransaksiTransfer($dataTransaksi, $dataDetail);

        if ($result['success']) {
            // Log activity
            $logModel = new LogAktivitas();
            $logModel->record(
                'PEMINDAHAN_BARANG',
                "Pemindahan barang #$nomorTransaksi berhasil. Qty: $qty.",
                'Transaksi',
                $nomorTransaksi
            );

            $_SESSION['success'] = "Pemindahan barang <strong>$nomorTransaksi</strong> berhasil disimpan!";
            header("Location: " . BASE_URL . "/transaksi/transfer");
        } else {
            $_SESSION['error'] = "Gagal: " . $result['message'];
            header("Location: " . BASE_URL . "/transaksi/transfer");
        }
        exit;
    }
}
