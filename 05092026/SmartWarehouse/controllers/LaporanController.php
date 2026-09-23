<?php
require_once '../helpers/AuthHelper.php';
require_once '../models/Laporan.php';
require_once '../models/Barang.php';
require_once '../models/Kategori.php';
require_once '../models/Lokasi.php';
require_once '../models/User.php';

class LaporanController {

    private $laporanModel;
    private $barangList;
    private $kategoriList;
    private $lokasiList;
    private $userList;

    public function __construct() {
        AuthHelper::requireRole(['Admin', 'Supervisor']);
        $this->laporanModel = new Laporan();
    }

    // Load shared filter data
    private function loadFilterData() {
        $this->barangList   = (new Barang())->getAll();
        $this->kategoriList = (new Kategori())->getAll();
        $this->lokasiList   = (new Lokasi())->getAll();
        $this->userList     = (new User())->getAll();
    }

    // Parse & sanitize filters from GET
    private function getFilters() {
        return [
            'date_from'   => $_GET['date_from']   ?? '',
            'date_to'     => $_GET['date_to']     ?? '',
            'barang_id'   => $_GET['barang_id']   ?? '',
            'kategori_id' => $_GET['kategori_id'] ?? '',
            'lokasi_id'   => $_GET['lokasi_id']   ?? '',
            'user_id'     => $_GET['user_id']     ?? '',
            'status'      => $_GET['status']      ?? '',
            'action'      => $_GET['action']      ?? '',
        ];
    }

    private function render($view, $title, $data = []) {
        $this->loadFilterData();
        extract($data);
        $barangList   = $this->barangList;
        $kategoriList = $this->kategoriList;
        $lokasiList   = $this->lokasiList;
        $userList     = $this->userList;

        require_once '../views/layouts/header.php';
        require_once '../views/layouts/sidebar.php';
        require_once $view;
        require_once '../views/layouts/footer.php';
    }

    // =============================================
    // 1. Laporan Stok
    // =============================================
    public function stok() {
        $filters = $this->getFilters();
        $rows    = $this->laporanModel->getLaporanStok($filters);
        $this->render('../views/laporan/stok.php', 'Laporan Stok - ' . APP_NAME, compact('filters', 'rows'));
    }

    // =============================================
    // 2. Laporan Barang Masuk
    // =============================================
    public function masuk() {
        $filters = $this->getFilters();
        $rows    = $this->laporanModel->getLaporanMasuk($filters);
        $this->render('../views/laporan/masuk.php', 'Laporan Barang Masuk - ' . APP_NAME, compact('filters', 'rows'));
    }

    // =============================================
    // 3. Laporan Barang Keluar
    // =============================================
    public function keluar() {
        $filters = $this->getFilters();
        $rows    = $this->laporanModel->getLaporanKeluar($filters);
        $this->render('../views/laporan/keluar.php', 'Laporan Barang Keluar - ' . APP_NAME, compact('filters', 'rows'));
    }

    // =============================================
    // 4. Laporan Pemindahan
    // =============================================
    public function pemindahan() {
        $filters = $this->getFilters();
        $rows    = $this->laporanModel->getLaporanPemindahan($filters);
        $this->render('../views/laporan/pemindahan.php', 'Laporan Pemindahan - ' . APP_NAME, compact('filters', 'rows'));
    }

    // =============================================
    // =============================================
    // 5. Laporan Aktivitas Gudang
    // =============================================
    public function aktivitas() {
        $filters = $this->getFilters();
        $rows    = $this->laporanModel->getLaporanAktivitas($filters);
        $this->render('../views/laporan/aktivitas.php', 'Laporan Aktivitas - ' . APP_NAME, compact('filters', 'rows'));
    }

    // =============================================
    // 6. Laporan AI Harian
    // =============================================
    public function ai_summary() {
        $selectedDate = $_GET['date'] ?? date('Y-m-d');
        
        $filters = [
            'date_from' => $selectedDate,
            'date_to'   => $selectedDate
        ];
        
        $rows = $this->laporanModel->getLaporanAktivitas($filters);
        $aiSummary = null;
        $errorMsg = null;

        if (isset($_POST['generate_ai'])) {
            if (empty($rows)) {
                $errorMsg = "Tidak ada aktivitas pada tanggal ini untuk diringkas.";
            } else {
                $apiKey = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '';
                if (empty($apiKey) || $apiKey === 'YOUR_GEMINI_API_KEY_HERE') {
                    $errorMsg = "Gemini API Key belum dikonfigurasi di config.php.";
                } else {
                    // Prepare data for AI
                    $logData = [];
                    foreach ($rows as $r) {
                        $logData[] = sprintf("[%s] %s (%s) melakukan: %s. Detail: %s", 
                            date('H:i:s', strtotime($r['created_at'])), 
                            $r['operator'], 
                            $r['action'], 
                            $r['description'], 
                            $r['related_data'] ?? '-'
                        );
                    }
                    
                    $prompt = "Berikut adalah log aktivitas sistem warehouse pada tanggal " . $selectedDate . ":\n" . 
                              implode("\n", $logData) . 
                              "\n\nBuatlah laporan ringkasan yang singkat, profesional, dan mudah dibaca (gunakan bahasa Indonesia) dari seluruh aktivitas di atas. Kelompokkan berdasarkan jenis aktivitas atau user jika perlu. Jangan membuat pendahuluan/penutup yang berlebihan, langsung ke poin-poin. Gunakan format HTML sederhana (seperti <h3>, <ul>, <li>, <strong>, <br>) untuk struktur laporan agar bisa langsung ditampilkan di web. Jangan gunakan tag ```html.";
                    
                    // Call Gemini API
                    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key=' . $apiKey;
                    
                    $data = [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $prompt]
                                ]
                            ]
                        ]
                    ];
                    
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    
                    // Supress SSL verification for local dev (xampp)
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                    
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    
                    if ($httpCode === 200 && $response) {
                        $result = json_decode($response, true);
                        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                            $aiSummary = $result['candidates'][0]['content']['parts'][0]['text'];
                        } else {
                            $errorMsg = "Gagal mengambil summary dari AI. Response tidak valid.";
                        }
                    } else {
                        $errorMsg = "Error memanggil Gemini API. HTTP Code: " . $httpCode;
                        if ($response) {
                            $errDetail = json_decode($response, true);
                            if (isset($errDetail['error']['message'])) {
                                $errorMsg .= " - " . $errDetail['error']['message'];
                            }
                        }
                    }
                }
            }
        }

        $this->render('../views/laporan/ai_summary.php', 'Laporan AI Harian - ' . APP_NAME, compact('selectedDate', 'rows', 'aiSummary', 'errorMsg'));
    }
}
