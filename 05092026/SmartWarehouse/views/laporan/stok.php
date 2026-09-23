<?php
$reportType  = 'stok';
$reportTitle = 'Laporan Stok Barang';
$title       = $reportTitle . ' - ' . APP_NAME;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">
            <i class="fas fa-layer-group text-info me-2"></i><?php echo $reportTitle; ?>
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

<?php $showStatus = true; require_once __DIR__ . '/_filter.php'; ?>
<?php require_once __DIR__ . '/_toolbar.php'; ?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="laporanTable">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th class="text-center">Total Stok</th>
                        <th class="text-center">Min. Stok</th>
                        <th class="text-center">Satuan</th>
                        <th class="text-center">Status</th>
                        <th>Lokasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr><td colspan="9" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fs-1 d-block mb-2 opacity-25"></i>Tidak ada data.
                        </td></tr>
                    <?php else: ?>
                        <?php foreach ($rows as $i => $r):
                            $qty      = (int)$r['total_qty'];
                            $minStock = (int)$r['min_stock'];
                            if ($qty === 0)           { $sc = 'danger';  $st = 'HABIS';   $si = 'times-circle'; }
                            elseif ($qty <= $minStock) { $sc = 'warning'; $st = 'MINIMUM'; $si = 'exclamation-triangle'; }
                            else                       { $sc = 'success'; $st = 'AMAN';    $si = 'check-circle'; }
                        ?>
                        <tr class="<?php echo $qty===0 ? 'table-danger' : ($qty<=$minStock ? 'table-warning' : ''); ?>">
                            <td class="ps-4 text-muted small"><?php echo $i+1; ?></td>
                            <td><code class="text-primary fw-bold"><?php echo htmlspecialchars($r['code']); ?></code></td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($r['barang_name']); ?></td>
                            <td><span class="badge bg-secondary bg-opacity-10 text-secondary border"><?php echo htmlspecialchars($r['kategori_name']); ?></span></td>
                            <td class="text-center fw-bold fs-5 text-<?php echo $sc; ?>"><?php echo number_format($qty); ?></td>
                            <td class="text-center text-muted"><?php echo number_format($minStock); ?></td>
                            <td class="text-center"><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($r['unit']); ?></span></td>
                            <td class="text-center">
                                <span class="badge bg-<?php echo $sc; ?>">
                                    <i class="fas fa-<?php echo $si; ?> me-1"></i><?php echo $st; ?>
                                </span>
                            </td>
                            <td class="small text-muted"><?php echo htmlspecialchars($r['lokasi_list'] ?? '-'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($rows)): ?>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="4" class="ps-4">Total</td>
                        <td class="text-center"><?php echo number_format(array_sum(array_column($rows, 'total_qty'))); ?></td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
