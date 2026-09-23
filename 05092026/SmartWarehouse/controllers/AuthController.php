<?php
require_once '../models/User.php';
require_once '../helpers/AuthHelper.php';

class AuthController {
    
    public function index() {
        $this->login();
    }

    public function login() {
        AuthHelper::redirectBasedOnRole(); // Redirect if already logged in
        
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $error = 'Username/Email and Password are required.';
            } else {
                $userModel = new User();
                $loginResult = $userModel->login($username, $password);
                if (is_array($loginResult) && isset($loginResult['success']) && $loginResult['success']) {
                    
                    // Session Security: Regenerate ID to prevent fixation
                    session_regenerate_id(true);
                    
                    // Log Aktivitas
                    require_once '../models/LogAktivitas.php';
                    $log = new LogAktivitas();
                    $log->record('login', 'User berhasil login', 'Auth', null);

                    AuthHelper::redirectBasedOnRole();
                } else if (is_array($loginResult)) {
                    $error = $loginResult['message'];
                } else {
                    $error = 'Invalid credentials.';
                }
            }
        }

        $title = "Login - " . APP_NAME;
        require_once '../views/auth/login.php';
    }

    public function logout() {
        // Log Aktivitas
        if (isset($_SESSION['user_id'])) {
            require_once '../models/LogAktivitas.php';
            $log = new LogAktivitas();
            $log->record('logout', 'User logout dari sistem', 'Auth', null);
        }

        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
    }
}
