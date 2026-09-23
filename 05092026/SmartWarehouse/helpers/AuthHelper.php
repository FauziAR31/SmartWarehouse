<?php

class AuthHelper {
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    public static function requireRole($allowedRoles) {
        self::requireLogin();
        if (!in_array($_SESSION['role_name'], $allowedRoles)) {
            // Redirect to a default page or show forbidden
            header('Location: ' . BASE_URL . '/home/index');
            exit;
        }
    }

    public static function redirectBasedOnRole() {
        if (self::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/home/index');
            exit;
        }
    }
}
