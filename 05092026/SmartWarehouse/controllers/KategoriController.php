<?php
require_once '../helpers/AuthHelper.php';
require_once '../models/Kategori.php';

class KategoriController {

    public function __construct() {
        // Only allow Admin and Supervisor to access Master Data Kategori
        AuthHelper::requireRole(['Admin', 'Supervisor']);
    }

    public function index() {
        $kategoriModel = new Kategori();
        $search = $_GET['search'] ?? '';
        $categories = $kategoriModel->getAll($search);

        $title = "Master Data Kategori - " . APP_NAME;
        require_once '../views/layouts/header.php';
        require_once '../views/layouts/sidebar.php';
        require_once '../views/kategori/index.php';
        require_once '../views/layouts/footer.php';
    }

    public function create() {
        $title = "Tambah Kategori - " . APP_NAME;
        require_once '../views/layouts/header.php';
        require_once '../views/layouts/sidebar.php';
        require_once '../views/kategori/form.php';
        require_once '../views/layouts/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description'])
            ];

            if (empty($data['name'])) {
                $_SESSION['error'] = "Nama Kategori wajib diisi!";
                header("Location: " . BASE_URL . "/kategori/create");
                exit;
            }

            $kategoriModel = new Kategori();
            if ($kategoriModel->create($data)) {
                $_SESSION['success'] = "Kategori berhasil ditambahkan.";
                header("Location: " . BASE_URL . "/kategori/index");
            } else {
                $_SESSION['error'] = "Gagal menambahkan kategori.";
                header("Location: " . BASE_URL . "/kategori/create");
            }
            exit;
        }
    }

    public function edit($id = null) {
        if (!$id) {
            header("Location: " . BASE_URL . "/kategori/index");
            exit;
        }

        $kategoriModel = new Kategori();
        $kategori = $kategoriModel->getById($id);

        if (!$kategori) {
            header("Location: " . BASE_URL . "/kategori/index");
            exit;
        }

        $title = "Edit Kategori - " . APP_NAME;
        require_once '../views/layouts/header.php';
        require_once '../views/layouts/sidebar.php';
        require_once '../views/kategori/form.php';
        require_once '../views/layouts/footer.php';
    }

    public function update($id = null) {
        if (!$id) {
            header("Location: " . BASE_URL . "/kategori/index");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description'])
            ];

            if (empty($data['name'])) {
                $_SESSION['error'] = "Nama Kategori wajib diisi!";
                header("Location: " . BASE_URL . "/kategori/edit/$id");
                exit;
            }

            $kategoriModel = new Kategori();
            if ($kategoriModel->update($id, $data)) {
                $_SESSION['success'] = "Kategori berhasil diperbarui.";
                header("Location: " . BASE_URL . "/kategori/index");
            } else {
                $_SESSION['error'] = "Gagal memperbarui kategori.";
                header("Location: " . BASE_URL . "/kategori/edit/$id");
            }
            exit;
        }
    }

    public function delete($id = null) {
        if ($id) {
            $kategoriModel = new Kategori();
            if ($kategoriModel->delete($id)) {
                $_SESSION['success'] = "Kategori berhasil dihapus.";
            } else {
                $_SESSION['error'] = "Gagal menghapus kategori! Kategori ini mungkin sedang digunakan oleh satu atau lebih barang.";
            }
        }
        header("Location: " . BASE_URL . "/kategori/index");
        exit;
    }
}
