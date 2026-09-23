<?php
require_once '../helpers/AuthHelper.php';
require_once '../models/User.php';

class UserController {

    private $userModel;

    public function __construct() {
        // Only Admin can access User Management
        AuthHelper::requireRole(['Admin']);
        $this->userModel = new User();
    }

    public function index() {
        $users = $this->userModel->getAllUsers();
        $roles = $this->userModel->getAllRoles();
        $title = "Manajemen User - " . APP_NAME;
        
        require_once '../views/layouts/header.php';
        require_once '../views/layouts/sidebar.php';
        require_once '../views/user/index.php';
        require_once '../views/layouts/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'full_name' => trim($_POST['full_name'] ?? ''),
                'username'  => trim($_POST['username'] ?? ''),
                'email'     => trim($_POST['email'] ?? ''),
                'password'  => $_POST['password'] ?? '',
                'role_id'   => $_POST['role_id'] ?? '',
                'status'    => $_POST['status'] ?? 'active'
            ];

            // Validation
            if (empty($data['full_name']) || empty($data['username']) || empty($data['email']) || empty($data['password']) || empty($data['role_id'])) {
                $_SESSION['error_message'] = "Semua field harus diisi.";
                header("Location: " . BASE_URL . "/user/index");
                exit;
            }

            // Check unique
            if ($this->userModel->checkUnique($data['username'], $data['email'])) {
                $_SESSION['error_message'] = "Username atau Email sudah terdaftar.";
                header("Location: " . BASE_URL . "/user/index");
                exit;
            }

            // Hash password
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

            if ($this->userModel->create($data)) {
                require_once '../models/LogAktivitas.php';
                $log = new LogAktivitas();
                $log->record('Tambah User', "Admin menambah user baru: {$data['username']}", 'User Management', $data['username']);
                $_SESSION['success_message'] = "User berhasil ditambahkan.";
            } else {
                $_SESSION['error_message'] = "Gagal menambahkan user.";
            }
        }
        header("Location: " . BASE_URL . "/user/index");
        exit;
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';
            $data = [
                'full_name' => trim($_POST['full_name'] ?? ''),
                'username'  => trim($_POST['username'] ?? ''),
                'email'     => trim($_POST['email'] ?? ''),
                'role_id'   => $_POST['role_id'] ?? '',
                'status'    => $_POST['status'] ?? 'active'
            ];

            if (empty($id) || empty($data['full_name']) || empty($data['username']) || empty($data['email']) || empty($data['role_id'])) {
                $_SESSION['error_message'] = "Semua field harus diisi.";
                header("Location: " . BASE_URL . "/user/index");
                exit;
            }

            // Check unique (excluding current user ID)
            if ($this->userModel->checkUnique($data['username'], $data['email'], $id)) {
                $_SESSION['error_message'] = "Username atau Email sudah dipakai oleh user lain.";
                header("Location: " . BASE_URL . "/user/index");
                exit;
            }

            // Admin cannot deactivate themselves (optional safeguard)
            if ($id == $_SESSION['user_id'] && $data['status'] === 'inactive') {
                $_SESSION['error_message'] = "Anda tidak dapat menonaktifkan akun Anda sendiri.";
                header("Location: " . BASE_URL . "/user/index");
                exit;
            }

            if ($this->userModel->update($id, $data)) {
                require_once '../models/LogAktivitas.php';
                $log = new LogAktivitas();
                $log->record('Edit User', "Admin memperbarui data user: {$data['username']}", 'User Management', $data['username']);
                $_SESSION['success_message'] = "Data user berhasil diperbarui.";
            } else {
                $_SESSION['error_message'] = "Gagal memperbarui data user.";
            }
        }
        header("Location: " . BASE_URL . "/user/index");
        exit;
    }

    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';

            if (empty($id) || empty($newPassword)) {
                $_SESSION['error_message'] = "Password baru tidak boleh kosong.";
                header("Location: " . BASE_URL . "/user/index");
                exit;
            }

            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            if ($this->userModel->updatePassword($id, $hashedPassword)) {
                require_once '../models/LogAktivitas.php';
                $log = new LogAktivitas();
                $log->record('Reset Password User', "Admin me-reset password user ID: $id", 'User Management', "user_id:$id");
                $_SESSION['success_message'] = "Password user berhasil di-reset.";
            } else {
                $_SESSION['error_message'] = "Gagal me-reset password.";
            }
        }
        header("Location: " . BASE_URL . "/user/index");
        exit;
    }
}
