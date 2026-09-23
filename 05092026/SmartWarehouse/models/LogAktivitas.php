<?php

class LogAktivitas {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function record($action, $description, $module = null, $relatedData = null) {
        $query = "INSERT INTO log_aktivitas (user_id, action, module, description, related_data, ip_address) 
                  VALUES (:user_id, :action, :module, :description, :related_data, :ip_address)";
        
        $stmt = $this->db->prepare($query);
        
        $userId = $_SESSION['user_id'] ?? null;
        
        require_once __DIR__ . '/../helpers/SecurityHelper.php';
        $ipAddress = SecurityHelper::getClientIp();

        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':module', $module);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':related_data', $relatedData);
        $stmt->bindParam(':ip_address', $ipAddress);
        
        return $stmt->execute();
    }

    public function getAll($limit = 50) {
        $query = "SELECT l.*, u.username, u.full_name 
                  FROM log_aktivitas l 
                  LEFT JOIN users u ON u.id = l.user_id 
                  ORDER BY l.created_at DESC 
                  LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
