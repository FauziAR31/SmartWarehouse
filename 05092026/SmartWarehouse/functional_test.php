<?php
// Automated functional test for SmartWarehouse
define('BASE_URL', 'http://localhost/05092026/SmartWarehouse');
define('APP_NAME', 'SmartWarehouse');
session_start();

// Fake session for tests
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['full_name'] = 'Administrator';
$_SESSION['role_id'] = 1;
$_SESSION['role_name'] = 'Admin';

require_once 'd:/xampp/htdocs/05092026/SmartWarehouse/config/database.php';
require_once 'd:/xampp/htdocs/05092026/SmartWarehouse/helpers/SecurityHelper.php';

$pass = 0;
$fail = 0;

function test($label, $callable) {
    global $pass, $fail;
    try {
        $result = $callable();
        if ($result !== false && $result !== null) {
            echo "[PASS] $label\n";
            $pass++;
        } else {
            echo "[FAIL] $label (returned false/null)\n";
            $fail++;
        }
    } catch (Throwable $e) {
        echo "[FAIL] $label - Exception: " . $e->getMessage() . "\n";
        $fail++;
    }
}

// --- Models ---
require_once 'd:/xampp/htdocs/05092026/SmartWarehouse/models/Barang.php';
require_once 'd:/xampp/htdocs/05092026/SmartWarehouse/models/Kategori.php';
require_once 'd:/xampp/htdocs/05092026/SmartWarehouse/models/Lokasi.php';
require_once 'd:/xampp/htdocs/05092026/SmartWarehouse/models/Transaksi.php';
require_once 'd:/xampp/htdocs/05092026/SmartWarehouse/models/Stok.php';
require_once 'd:/xampp/htdocs/05092026/SmartWarehouse/models/User.php';
require_once 'd:/xampp/htdocs/05092026/SmartWarehouse/models/LogAktivitas.php';
require_once 'd:/xampp/htdocs/05092026/SmartWarehouse/models/Dashboard.php';
require_once 'd:/xampp/htdocs/05092026/SmartWarehouse/models/Laporan.php';

$barang = new Barang();
$kategori = new Kategori();
$lokasi = new Lokasi();
$transaksi = new Transaksi();
$stok = new Stok();
$user = new User();
$log = new LogAktivitas();
$dashboard = new Dashboard();
$laporan = new Laporan();

echo "\n=== MODEL TESTS ===\n";

// Barang
test("Barang::getAllPaginated()", fn() => $barang->getAllPaginated(10, 0));
test("Barang::getAll()", fn() => $barang->getAll());
test("Barang::getAllActive()", fn() => is_array($barang->getAllActive()));
test("Barang::getTotalRows()", fn() => is_numeric($barang->getTotalRows()));

// Get first barang for sub-tests
$barangs = $barang->getAllPaginated(1, 0);
if (!empty($barangs)) {
    $b = $barangs[0];
    test("Barang::getById()", fn() => $barang->getById($b['id']));
    test("Barang::isCodeUnique() (existing should be false)", fn() => !$barang->isCodeUnique($b['code']));
    test("Barang::isCodeUnique() (new should be true)", fn() => $barang->isCodeUnique('ZZZTEST999'));
    test("Barang::getAvailableLocations()", fn() => is_array($barang->getAvailableLocations($b['id'])));
}

// Kategori
test("Kategori::getAll()", fn() => $kategori->getAll());
test("Kategori::getTotalRows()", fn() => is_numeric($kategori->getTotalRows()));

// Lokasi
test("Lokasi::getAll()", fn() => is_array($lokasi->getAll()));
test("Lokasi::getTotalRows()", fn() => is_numeric($lokasi->getTotalRows()));

// Stok
test("Stok::getStockReport()", fn() => is_array($stok->getStockReport('','','','',10,0)));
test("Stok::countStockReport()", fn() => is_numeric($stok->countStockReport()));

// Transaksi
test("Transaksi::generateTransactionNumber('IN')", fn() => strlen($transaksi->generateTransactionNumber('IN')) > 5);
test("Transaksi::generateTransactionNumber('OUT')", fn() => strlen($transaksi->generateTransactionNumber('OUT')) > 5);
test("Transaksi::generateTransactionNumber('TRF')", fn() => strlen($transaksi->generateTransactionNumber('TRF')) > 5);
test("Transaksi::getRiwayat()", fn() => is_array($transaksi->getRiwayat([], 10, 0)));
test("Transaksi::countRiwayat()", fn() => is_numeric($transaksi->countRiwayat()));

// User
test("User::getAll()", fn() => is_array($user->getAll()));
test("User::getAllUsers()", fn() => is_array($user->getAllUsers()));
test("User::getAllRoles()", fn() => count($user->getAllRoles()) > 0);

// Dashboard
test("Dashboard::getTotalBarang()", fn() => is_numeric($dashboard->getTotalBarang()));
test("Dashboard::getTotalStok()", fn() => is_numeric($dashboard->getTotalStok()));
test("Dashboard::getTotalLokasi()", fn() => is_numeric($dashboard->getTotalLokasi()));
test("Dashboard::getBarangMasukHariIni()", fn() => is_numeric($dashboard->getBarangMasukHariIni()));
test("Dashboard::getBarangKeluarHariIni()", fn() => is_numeric($dashboard->getBarangKeluarHariIni()));
test("Dashboard::getBarangStokMinimum()", fn() => is_numeric($dashboard->getBarangStokMinimum()));
test("Dashboard::getBarangHabis()", fn() => is_numeric($dashboard->getBarangHabis()));
test("Dashboard::getRecentTransactions()", fn() => is_array($dashboard->getRecentTransactions()));
test("Dashboard::getChartData()", fn() => is_array($dashboard->getChartData()));

// Laporan
test("Laporan::getLaporanStok()", fn() => is_array($laporan->getLaporanStok()));
test("Laporan::getLaporanMasuk()", fn() => is_array($laporan->getLaporanMasuk()));
test("Laporan::getLaporanKeluar()", fn() => is_array($laporan->getLaporanKeluar()));
test("Laporan::getLaporanPemindahan()", fn() => is_array($laporan->getLaporanPemindahan()));
test("Laporan::getLaporanAktivitas()", fn() => is_array($laporan->getLaporanAktivitas()));

// LogAktivitas
test("LogAktivitas::getAll()", fn() => is_array($log->getAll()));

// Security
test("SecurityHelper::getClientIp()", fn() => is_string(SecurityHelper::getClientIp()));
test("SecurityHelper::csrfToken()", fn() => strlen(SecurityHelper::csrfToken()) > 10);

echo "\n=== SUMMARY ===\n";
echo "PASSED: $pass\n";
echo "FAILED: $fail\n";
echo $fail === 0 ? "ALL TESTS PASSED!\n" : "FIX REQUIRED!\n";
