<?php
define('BASE_URL','http://localhost');
define('APP_NAME','SW');
require_once 'd:/xampp/htdocs/05092026/SmartWarehouse/config/database.php';
try {
    $db = (new Database())->getConnection();
    
    $checks = [
        "barang.status"             => "SHOW COLUMNS FROM barang LIKE 'status'",
        "transaksi.supplier_name"   => "SHOW COLUMNS FROM transaksi LIKE 'supplier_name'",
        "transaksi.recipient_name"  => "SHOW COLUMNS FROM transaksi LIKE 'recipient_name'",
        "log_aktivitas.module"      => "SHOW COLUMNS FROM log_aktivitas LIKE 'module'",
        "log_aktivitas.related_data"=> "SHOW COLUMNS FROM log_aktivitas LIKE 'related_data'",
        "transaksi_detail.lokasi_tujuan_id" => "SHOW COLUMNS FROM transaksi_detail LIKE 'lokasi_tujuan_id'",
    ];
    
    foreach ($checks as $label => $q) {
        $r = $db->query($q)->rowCount();
        echo "$label: ".($r ? 'YES' : 'NO').PHP_EOL;
    }
    
    $row = $db->query("SELECT COUNT(*) as c FROM barang")->fetch(PDO::FETCH_ASSOC);
    echo "barang rows: ".$row['c'].PHP_EOL;
    
    $row2 = $db->query("SELECT COUNT(*) as c FROM users")->fetch(PDO::FETCH_ASSOC);
    echo "users rows: ".$row2['c'].PHP_EOL;
    
    // Check if status column values in barang
    $statuses = $db->query("SELECT DISTINCT status FROM barang LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    echo "barang status values: ";
    if (empty($statuses)) { echo "(empty)"; }
    foreach ($statuses as $s) { echo $s['status']." "; }
    echo PHP_EOL;
    
    echo "ALL CHECKS DONE".PHP_EOL;
} catch(Exception $e) { echo "Error: ".$e->getMessage().PHP_EOL; }
