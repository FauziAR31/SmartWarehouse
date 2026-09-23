<?php
$reportType  = 'keluar';
$reportTitle = 'Laporan Barang Keluar';
$title       = $reportTitle . ' - ' . APP_NAME;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">
            <i class="fas fa-arrow-circle-up text-danger me-2"></i><?php echo $reportTitle; ?>
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item">Laporan</li>
                <li class="breadcrumb-item active"><?php echo $reportTitle; ?></li>
            </ol>
        </nav>
    </div>
</div>

<?php require_once __DIR__ . '/_filter.php'; ?>
<?php require_once __DIR__ . '/_toolbar.php'; ?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="laporanTable">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>No. Transaksi</th>
                        <th>Tanggal</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-center">Satuan</th>
                        <th>Lokasi Asal</th>
                        <th>Tujuan / Penerima</th>
                        <th>Operator</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr><td colspan="12" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fs-1 d-block mb-2 opacity-25"></i>Tidak ada data.
                        </td></tr>
                    <?php else: ?>
                        <?php foreach ($rows as $i => $r): ?>
                        <tr>
                            <td class="ps-4 text-muted small"><?php echo $i+1; ?></td>
                            <td><code class="text-danger fw-bold small"><?php echo htmlspecialchars($r['transaction_number']); ?></code></td>
                            <td class="small">
                                <div><?php echo date('d/m/Y', strtotime($r['transaction_date'])); ?></div>
                                <small class="text-muted"><?php echo date('H:i', strtotime($r['transaction_date'])); ?></small>
                            </td>
                            <td><code class="text-primary small"><?php echo htmlspecialchars($r['barang_code']); ?></code></td>
                            <td class="fw-semibold small"><?php echo htmlspecialchars($r['barang_name']); ?></td>
                            <td><span class="badge bg-light text-dark border small"><?php echo htmlspecialchars($r['kategori_name']); ?></span></td>
                            <td class="text-center fw-bold text-danger"><?php echo number_format($r['qty']); ?></td>
                            <td class="text-center"><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($r['unit']); ?></span></td>
                            <td class="small">
                                <code><?php echo htmlspecialchars($r['lokasi_kode']); ?></code><br>
                                <small class="text-muted"><?php echo htmlspecialchars($r['lokasi_nama']); ?></small>
                            </td>
                            <td class="small"><?php echo htmlspecialchars($r['recipient_name'] ?? '-'); ?></td>
                            <td class="small"><?php echo htmlspecialchars($r['operator']); ?></td>
                            <td class="small text-muted"><?php echo htmlspecialchars($r['notes'] ?? '-'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($rows)): ?>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="6" class="ps-4">Total</td>
                        <td class="text-center text-danger"><?php echo number_format(array_sum(array_column($rows, 'qty'))); ?></td>
                        <td colspan="5"></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
