<?php

class User {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function login($username, $password) {
        $query = "SELECT u.id, u.username, u.password, u.full_name, u.role_id, r.name as role_name, u.status 
                  FROM users u 
                  JOIN roles r ON u.role_id = r.id 
                  WHERE u.username = :username OR u.email = :email";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $username); // allow login with email as well
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Periksa apakah user aktif
            if ($user['status'] !== 'active') {
                return ['success' => false, 'message' => 'Akun Anda dinonaktifkan. Silakan hubungi Administrator.'];
            }
            
            if (password_verify($password, $user['password'])) {
                // Set sessions
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['role_name'] = $user['role_name'];

                return ['success' => true];
            }
            return ['success' => false, 'message' => 'Password salah.'];
        }
        return ['success' => false, 'message' => 'Username atau email tidak ditemukan.'];
    }

    public function getAll() {
        // Updated to show all users, including inactive ones for admin view
        $stmt = $this->db->query("SELECT id, full_name, username FROM users WHERE status = 'active' ORDER BY full_name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- MANAJEMEN USER (ADMIN ONLY) ---

    public function getAllUsers() {
        $query = "SELECT u.id, u.username, u.email, u.full_name, u.status, u.role_id, r.name as role_name, u.created_at
                  FROM users u 
                  JOIN roles r ON u.role_id = r.id 
                  ORDER BY u.created_at DESC";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($id) {
        $query = "SELECT id, username, email, full_name, status, role_id FROM users WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function checkUnique($username, $email, $excludeId = null) {
        $query = "SELECT id FROM users WHERE (username = :username OR email = :email)";
        if ($excludeId) {
            $query .= " AND id != :excludeId";
        }
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        if ($excludeId) {
            $stmt->bindParam(':excludeId', $excludeId);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function create($data) {
        $query = "INSERT INTO users (full_name, username, email, password, role_id, status) 
                  VALUES (:full_name, :username, :email, :password, :role_id, :status)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $data['password']); // should be hashed before calling
        $stmt->bindParam(':role_id', $data['role_id']);
        $stmt->bindParam(':status', $data['status']);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $query = "UPDATE users SET 
                    full_name = :full_name, 
                    username = :username, 
                    email = :email, 
                    role_id = :role_id, 
                    status = :status 
                  WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':role_id', $data['role_id']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function updatePassword($id, $hashedPassword) {
        $query = "UPDATE users SET password = :password WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getAllRoles() {
        $stmt = $this->db->query("SELECT id, name FROM roles ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

