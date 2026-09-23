CREATE DATABASE IF NOT EXISTS `smartwarehouse`;
USE `smartwarehouse`;

SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------
-- Table `roles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL,
    `description` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `role_id` INT NOT NULL,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `full_name` VARCHAR(100) NOT NULL,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX `idx_users_role` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `kategori`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `kategori`;
CREATE TABLE `kategori` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `lokasi`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `lokasi`;
CREATE TABLE `lokasi` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `barang`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `barang`;
CREATE TABLE `barang` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(150) NOT NULL,
    `kategori_id` INT NOT NULL,
    `description` TEXT NULL,
    `unit` VARCHAR(20) NOT NULL COMMENT 'e.g., pcs, box, kg, unit',
    `min_stock` INT NOT NULL DEFAULT 5,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`kategori_id`) REFERENCES `kategori`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX `idx_barang_kategori` (`kategori_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `stok`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `stok`;
CREATE TABLE `stok` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `barang_id` INT NOT NULL,
    `lokasi_id` INT NOT NULL,
    `qty` INT NOT NULL DEFAULT 0,
    `last_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`barang_id`) REFERENCES `barang`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`lokasi_id`) REFERENCES `lokasi`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    UNIQUE KEY `unique_barang_lokasi` (`barang_id`, `lokasi_id`),
    INDEX `idx_stok_barang` (`barang_id`),
    INDEX `idx_stok_lokasi` (`lokasi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `transaksi`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `transaksi`;
CREATE TABLE `transaksi` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `transaction_number` VARCHAR(50) NOT NULL UNIQUE,
    `type` ENUM('in', 'out', 'transfer') NOT NULL,
    `user_id` INT NOT NULL,
    `supplier_name` VARCHAR(255) NULL,
    `recipient_name` VARCHAR(255) NULL,
    `transaction_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `notes` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX `idx_trans_type` (`type`),
    INDEX `idx_trans_date` (`transaction_date`),
    INDEX `idx_trans_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `transaksi_detail`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `transaksi_detail`;
CREATE TABLE `transaksi_detail` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `transaksi_id` INT NOT NULL,
    `barang_id` INT NOT NULL,
    `lokasi_id` INT NOT NULL,
    `lokasi_tujuan_id` INT NULL DEFAULT NULL,
    `qty` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`barang_id`) REFERENCES `barang`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (`lokasi_id`) REFERENCES `lokasi`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (`lokasi_tujuan_id`) REFERENCES `lokasi`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX `idx_td_transaksi` (`transaksi_id`),
    INDEX `idx_td_barang` (`barang_id`),
    INDEX `idx_td_lokasi` (`lokasi_id`),
    INDEX `idx_td_lokasi_tujuan` (`lokasi_tujuan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------
-- Table `log_aktivitas`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `log_aktivitas`;
CREATE TABLE `log_aktivitas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `action` VARCHAR(100) NOT NULL,
    `module` VARCHAR(50) NULL,
    `description` TEXT NOT NULL,
    `related_data` VARCHAR(100) NULL,
    `ip_address` VARCHAR(45) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_log_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;


-- =====================================================
-- SEED DATA (DUMMY DATA)
-- =====================================================

-- 1. Insert Roles
INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'Admin', 'Administrator with full system access'),
(2, 'Supervisor', 'Can oversee transactions, manage products, and generate reports'),
(3, 'Operator', 'Can process in and out stock transactions');

-- 2. Insert Users
-- Note: Password for all dummy users is 'password123'
-- Hashed using bcrypt: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
INSERT INTO `users` (`id`, `role_id`, `username`, `password`, `email`, `full_name`) VALUES
(1, 1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@smartwarehouse.com', 'Super Admin'),
(2, 2, 'supervisor', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'supervisor@smartwarehouse.com', 'John Supervisor'),
(3, 3, 'operator', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'operator@smartwarehouse.com', 'Jane Operator');

-- 3. Insert Kategori
INSERT INTO `kategori` (`id`, `name`, `description`) VALUES
(1, 'Elektronik', 'Peralatan elektronik dan gadget'),
(2, 'Pakaian', 'Pakaian pria, wanita, dan anak-anak'),
(3, 'Bahan Makanan', 'Produk konsumsi, makanan, dan minuman'),
(4, 'Alat Tulis Kantor', 'Kebutuhan perlengkapan alat tulis kantor (ATK)');

-- 4. Insert Lokasi
INSERT INTO `lokasi` (`id`, `name`, `description`) VALUES
(1, 'Gudang A - Rak 1', 'Penyimpanan barang elektronik berharga tinggi (Aman & Kering)'),
(2, 'Gudang A - Rak 2', 'Penyimpanan barang elektronik umum'),
(3, 'Gudang B - Blok 1', 'Penyimpanan tekstil dan pakaian jadi'),
(4, 'Gudang C - Pendingin', 'Ruangan khusus dengan suhu terjaga untuk bahan makanan'),
(5, 'Gudang D - Area Loading', 'Area sementara untuk transit barang');

-- 5. Insert Barang
INSERT INTO `barang` (`id`, `code`, `name`, `kategori_id`, `description`, `unit`) VALUES
(1, 'BRG-ELK-001', 'Laptop ASUS ROG Strix', 1, 'Laptop gaming high-end spesifikasi terbaru', 'unit'),
(2, 'BRG-ELK-002', 'Smartphone Samsung Galaxy S23', 1, 'Smartphone flagship', 'unit'),
(3, 'BRG-ELK-003', 'Mouse Wireless Logitech M280', 1, 'Mouse wireless ergonomis', 'pcs'),
(4, 'BRG-PAK-001', 'Kaos Polos Hitam Cotton Combed 30s - L', 2, 'Kaos bahan katun combed 30s ukuran L', 'pcs'),
(5, 'BRG-PAK-002', 'Kemeja Flanel Kotak-kotak', 2, 'Kemeja lengan panjang bahan flanel', 'pcs'),
(6, 'BRG-MAK-001', 'Susu UHT Full Cream 1 Liter', 3, 'Susu segar kemasan kotak karton', 'box'),
(7, 'BRG-MAK-002', 'Daging Sapi Beku 1 Kg', 3, 'Daging sapi segar yang dibekukan', 'kg'),
(8, 'BRG-ATK-001', 'Kertas HVS A4 80 GSM', 4, 'Kertas cetak ukuran A4', 'rim');

-- 6. Insert Stok Awal
INSERT INTO `stok` (`barang_id`, `lokasi_id`, `qty`) VALUES
(1, 1, 15),
(2, 1, 45),
(3, 2, 120),
(4, 3, 250),
(5, 3, 100),
(6, 4, 300),
(7, 4, 50),
(8, 2, 80);

-- 7. Insert Dummy Data Transaksi (Opsional untuk testing relasi)
INSERT INTO `transaksi` (`id`, `transaction_number`, `type`, `user_id`, `transaction_date`, `notes`) VALUES
(1, 'TRX-IN-20231001-0001', 'in', 2, '2023-10-01 08:30:00', 'Penerimaan barang dari supplier PT. Elektronik Jaya'),
(2, 'TRX-OUT-20231002-0001', 'out', 3, '2023-10-02 14:15:00', 'Pengiriman pesanan ke Toko Sentosa');

-- 8. Insert Dummy Data Transaksi Detail
INSERT INTO `transaksi_detail` (`transaksi_id`, `barang_id`, `lokasi_id`, `qty`) VALUES
(1, 1, 1, 5),
(1, 2, 1, 20),
(2, 3, 2, 10),
(2, 8, 2, 5);

-- 9. Insert Dummy Data Log Aktivitas
INSERT INTO `log_aktivitas` (`user_id`, `action`, `description`, `ip_address`) VALUES
(1, 'login', 'User admin berhasil login', '192.168.1.10'),
(2, 'create_transaksi', 'Membuat transaksi masuk TRX-IN-20231001-0001', '192.168.1.11'),
(3, 'create_transaksi', 'Membuat transaksi keluar TRX-OUT-20231002-0001', '192.168.1.12');
