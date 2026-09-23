<?php
require_once 'd:/xampp/htdocs/05092026/SmartWarehouse/config/database.php';
try {
    $db = (new Database())->getConnection();
    if (!$db) {
        throw new Exception("Could not connect to database. Please check your connection.");
    }
    
    // Add columns if they don't exist
    $check = $db->query("SHOW COLUMNS FROM `barang` LIKE 'min_stock'");
    if ($check->rowCount() == 0) {
        $db->exec("ALTER TABLE `barang` ADD COLUMN `min_stock` INT NOT NULL DEFAULT 10");
        echo "min_stock added.\n";
    }
    
    $checkStatus = $db->query("SHOW COLUMNS FROM `barang` LIKE 'status'");
    if ($checkStatus->rowCount() == 0) {
        $db->exec("ALTER TABLE `barang` ADD COLUMN `status` ENUM('active', 'inactive') DEFAULT 'active'");
        echo "status added.\n";
    }
    
    $checkSupplier = $db->query("SHOW COLUMNS FROM `transaksi` LIKE 'supplier_name'");
    if ($checkSupplier->rowCount() == 0) {
        $db->exec("ALTER TABLE `transaksi` ADD COLUMN `supplier_name` VARCHAR(255) NULL AFTER `user_id`");
        echo "supplier_name added to transaksi.\n";
    }

    $checkRecipient = $db->query("SHOW COLUMNS FROM `transaksi` LIKE 'recipient_name'");
    if ($checkRecipient->rowCount() == 0) {
        $db->exec("ALTER TABLE `transaksi` ADD COLUMN `recipient_name` VARCHAR(255) NULL AFTER `supplier_name`");
        echo "recipient_name added to transaksi.\n";
    }

    $checkType = $db->query("SHOW COLUMNS FROM `transaksi` LIKE 'type'");
    $typeRow = $checkType->fetch(PDO::FETCH_ASSOC);
    if (strpos($typeRow['Type'], 'transfer') === false) {
        $db->exec("ALTER TABLE `transaksi` MODIFY COLUMN `type` ENUM('in', 'out', 'transfer') NOT NULL");
        echo "type enum modified to include 'transfer'.\n";
    }

    $checkLokasiTujuan = $db->query("SHOW COLUMNS FROM `transaksi_detail` LIKE 'lokasi_tujuan_id'");
    if ($checkLokasiTujuan->rowCount() == 0) {
        $db->exec("ALTER TABLE `transaksi_detail` ADD COLUMN `lokasi_tujuan_id` INT NULL DEFAULT NULL AFTER `lokasi_id`");
        $db->exec("ALTER TABLE `transaksi_detail` ADD CONSTRAINT `fk_lokasi_tujuan` FOREIGN KEY (`lokasi_tujuan_id`) REFERENCES `lokasi`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE");
        echo "lokasi_tujuan_id added to transaksi_detail.\n";
    }

    $checkMinStock = $db->query("SHOW COLUMNS FROM `barang` LIKE 'min_stock'");
    if ($checkMinStock->rowCount() == 0) {
        $db->exec("ALTER TABLE `barang` ADD COLUMN `min_stock` INT NOT NULL DEFAULT 5 AFTER `unit`");
        echo "min_stock added to barang.\n";
    }

    $checkModule = $db->query("SHOW COLUMNS FROM `log_aktivitas` LIKE 'module'");
    if ($checkModule->rowCount() == 0) {
        $db->exec("ALTER TABLE `log_aktivitas` ADD COLUMN `module` VARCHAR(50) NULL AFTER `action`");
        echo "module added to log_aktivitas.\n";
    }

    $checkRelatedData = $db->query("SHOW COLUMNS FROM `log_aktivitas` LIKE 'related_data'");
    if ($checkRelatedData->rowCount() == 0) {
        $db->exec("ALTER TABLE `log_aktivitas` ADD COLUMN `related_data` VARCHAR(100) NULL AFTER `description`");
        echo "related_data added to log_aktivitas.\n";
    }

    echo "Database patch applied successfully.\n";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
