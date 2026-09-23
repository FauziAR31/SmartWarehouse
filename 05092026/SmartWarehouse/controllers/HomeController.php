<?php
require_once '../helpers/AuthHelper.php';
require_once '../models/Dashboard.php';

class HomeController {
    public function index() {
        AuthHelper::requireLogin();

        $dashboard = new Dashboard();
        
        $data = [
            'total_barang' => $dashboard->getTotalBarang(),
            'total_stok' => $dashboard->getTotalStok(),
            'barang_masuk_hari_ini' => $dashboard->getBarangMasukHariIni(),
            'barang_keluar_hari_ini' => $dashboard->getBarangKeluarHariIni(),
            'total_lokasi' => $dashboard->getTotalLokasi(),
            'stok_minimum' => $dashboard->getBarangStokMinimum(),
            'barang_habis' => $dashboard->getBarangHabis(),
            'recent_transactions' => $dashboard->getRecentTransactions(),
            'chart_data' => json_encode($dashboard->getChartData())
        ];

        $title = "Dashboard - " . APP_NAME;
        require_once '../views/layouts/header.php';
        require_once '../views/layouts/sidebar.php';
        require_once '../views/home/index.php';
        require_once '../views/layouts/footer.php';
    }
}
