<?php
$reportType  = 'aktivitas';
$reportTitle = 'Laporan Aktivitas Gudang';
$title       = $reportTitle . ' - ' . APP_NAME;
$showAction  = true;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">
            <i class="fas fa-clipboard-list text-secondary me-2"></i><?php echo $reportTitle; ?>
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
                        <th>Tanggal & Waktu</th>
                        <th>Operator</th>
                        <th>Modul</th>
                        <th>Aksi</th>
                        <th>Deskripsi</th>
                        <th>Data Terkait</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr><td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fs-1 d-block mb-2 opacity-25"></i>Tidak ada data aktivitas.
                        </td></tr>
                    <?php else: ?>
                        <?php foreach ($rows as $i => $r):
                            $actionColors = [
                                'login'              => 'info',
                                'logout'             => 'secondary',
                                'BARANG_MASUK'       => 'success',
                                'BARANG_KELUAR'      => 'danger',
                                'PEMINDAHAN_BARANG'  => 'warning',
                                'Tambah Barang'      => 'success',
                                'Edit Barang'        => 'primary',
                                'Hapus Barang'       => 'danger',
                                'Tambah User'        => 'success',
                                'Edit User'          => 'primary',
                                'Reset Password User'=> 'warning',
                            ];
                            $color = $actionColors[$r['action']] ?? 'secondary';
                        ?>
                        <tr>
                            <td class="ps-4 text-muted small"><?php echo $i+1; ?></td>
                            <td class="small">
                                <div class="fw-semibold"><?php echo date('d/m/Y', strtotime($r['created_at'])); ?></div>
                                <small class="text-muted"><?php echo date('H:i:s', strtotime($r['created_at'])); ?></small>
                            </td>
                            <td class="small">
                                <div class="fw-semibold"><?php echo htmlspecialchars($r['operator']); ?></div>
                                <small class="text-muted">@<?php echo htmlspecialchars($r['username']); ?></small>
                            </td>
                            <td class="small text-muted">
                                <?php echo htmlspecialchars($r['module'] ?? '-'); ?>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $color; ?> px-2 py-1 small">
                                    <?php echo htmlspecialchars($r['action']); ?>
                                </span>
                            </td>
                            <td class="small"><?php echo htmlspecialchars($r['description']); ?></td>
                            <td class="small">
                                <?php if (!empty($r['related_data'])): ?>
                                    <code class="text-primary small"><?php echo htmlspecialchars($r['related_data']); ?></code>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted font-monospace"><?php echo htmlspecialchars($r['ip_address'] ?? '-'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
