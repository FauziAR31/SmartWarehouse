<?php
// views/riwayat/index.php
// Variables: $riwayat, $totalRows, $totalPages, $page, $limit, $offset
//            $filters, $barangList, $userList
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">
            <i class="fas fa-history text-primary me-2"></i>Riwayat Transaksi
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item">Inventory</li>
                <li class="breadcrumb-item active">Riwayat Transaksi</li>
            </ol>
        </nav>
    </div>
    <span class="badge bg-primary fs-6 px-3 py-2">
        <i class="fas fa-list me-1"></i>Total: <?php echo number_format($totalRows); ?> Transaksi
    </span>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-transparent border-0 py-3 px-4">
        <h6 class="fw-bold mb-0"><i class="fas fa-filter text-primary me-2"></i>Filter & Pencarian</h6>
    </div>
    <div class="card-body pt-0 px-4 pb-3">
        <form method="GET" action="<?php echo BASE_URL; ?>/riwayat/index" id="filterForm">
            <div class="row g-2 mb-2">
                <!-- Search -->
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-muted">Cari</label>
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white border-0"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search"
                               placeholder="No. transaksi, nama / kode barang..."
                               value="<?php echo htmlspecialchars($filters['search']); ?>">
                    </div>
                </div>
                <!-- Jenis Transaksi -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-muted">Jenis</label>
                    <select class="form-select" name="type">
                        <option value="">Semua Jenis</option>
                        <option value="in"       <?php echo $filters['type']==='in'       ? 'selected':'' ?>>📥 Barang Masuk</option>
                        <option value="out"      <?php echo $filters['type']==='out'      ? 'selected':'' ?>>📤 Barang Keluar</option>
                        <option value="transfer" <?php echo $filters['type']==='transfer' ? 'selected':'' ?>>🔄 Pemindahan</option>
                    </select>
                </div>
                <!-- Barang -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted">Barang</label>
                    <select class="form-select" name="barang_id">
                        <option value="">Semua Barang</option>
                        <?php foreach ($barangList as $b): ?>
                            <option value="<?php echo $b['id']; ?>" <?php echo ($filters['barang_id'] == $b['id']) ? 'selected' : ''; ?>>
                                [<?php echo htmlspecialchars($b['code']); ?>] <?php echo htmlspecialchars($b['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Operator -->
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted">Operator</label>
                    <select class="form-select" name="user_id">
                        <option value="">Semua Operator</option>
                        <?php foreach ($userList as $u): ?>
                            <option value="<?php echo $u['id']; ?>" <?php echo ($filters['user_id'] == $u['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($u['full_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row g-2 align-items-end">
                <!-- Tanggal Dari -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-muted">Tanggal Dari</label>
                    <input type="date" class="form-control" name="date_from"
                           value="<?php echo htmlspecialchars($filters['date_from']); ?>">
                </div>
                <!-- Tanggal Sampai -->
                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-muted">Sampai</label>
                    <input type="date" class="form-control" name="date_to"
                           value="<?php echo htmlspecialchars($filters['date_to']); ?>">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="<?php echo BASE_URL; ?>/riwayat/index" class="btn btn-outline-secondary" title="Reset">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
                <div class="col text-end text-muted small">
                    <?php if (array_filter($filters)): ?>
                        <span class="badge bg-warning text-dark">
                            <i class="fas fa-filter me-1"></i>Filter aktif — menampilkan <?php echo number_format($totalRows); ?> hasil
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>No. Transaksi</th>
                        <th>Tanggal</th>
                        <th class="text-center">Jenis</th>
                        <th>Barang</th>
                        <th class="text-center">Jumlah</th>
                        <th>Lokasi</th>
                        <th>Operator</th>
                        <th>Keterangan</th>
                        <th class="text-center">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($riwayat)): ?>
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-history fs-1 mb-3 d-block opacity-25"></i>
                                    <p class="mb-0">Tidak ada data transaksi ditemukan.</p>
                                    <?php if (array_filter($filters)): ?>
                                        <a href="<?php echo BASE_URL; ?>/riwayat/index" class="btn btn-sm btn-outline-secondary mt-2">
                                            <i class="fas fa-undo me-1"></i>Reset Filter
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($riwayat as $i => $row): ?>
                            <?php
                            switch ($row['type']) {
                                case 'in':
                                    $typeLabel = '<span class="badge bg-success"><i class="fas fa-arrow-circle-down me-1"></i>Masuk</span>';
                                    $lokasiText = '<code>' . htmlspecialchars($row['lokasi_asal_kode']) . '</code><br><small class="text-muted">' . htmlspecialchars($row['lokasi_asal_nama']) . '</small>';
                                    break;
                                case 'out':
                                    $typeLabel = '<span class="badge bg-danger"><i class="fas fa-arrow-circle-up me-1"></i>Keluar</span>';
                                    $lokasiText = '<code>' . htmlspecialchars($row['lokasi_asal_kode']) . '</code><br><small class="text-muted">' . htmlspecialchars($row['lokasi_asal_nama']) . '</small>';
                                    break;
                                case 'transfer':
                                    $typeLabel = '<span class="badge bg-warning text-dark"><i class="fas fa-exchange-alt me-1"></i>Pindah</span>';
                                    $lokasiText = '<code>' . htmlspecialchars($row['lokasi_asal_kode']) . '</code>
                                                   <i class="fas fa-long-arrow-alt-right text-muted mx-1"></i>
                                                   <code class="text-success">' . htmlspecialchars($row['lokasi_tujuan_kode'] ?? '-') . '</code>';
                                    break;
                                default:
                                    $typeLabel = '<span class="badge bg-secondary">-</span>';
                                    $lokasiText = '-';
                            }
                            ?>
                            <tr>
                                <td class="ps-4 text-muted small"><?php echo $offset + $i + 1; ?></td>
                                <td>
                                    <code class="text-primary fw-bold small"><?php echo htmlspecialchars($row['transaction_number']); ?></code>
                                </td>
                                <td>
                                    <div class="fw-semibold small"><?php echo date('d/m/Y', strtotime($row['transaction_date'])); ?></div>
                                    <small class="text-muted"><?php echo date('H:i', strtotime($row['transaction_date'])); ?></small>
                                </td>
                                <td class="text-center"><?php echo $typeLabel; ?></td>
                                <td>
                                    <div class="fw-semibold small"><?php echo htmlspecialchars($row['barang_name']); ?></div>
                                    <small class="text-muted"><code><?php echo htmlspecialchars($row['barang_code']); ?></code></small>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold"><?php echo number_format($row['qty']); ?></span>
                                    <small class="text-muted d-block"><?php echo htmlspecialchars($row['barang_unit']); ?></small>
                                </td>
                                <td class="small"><?php echo $lokasiText; ?></td>
                                <td>
                                    <div class="fw-semibold small"><?php echo htmlspecialchars($row['operator']); ?></div>
                                </td>
                                <td class="small text-muted" style="max-width:150px;">
                                    <?php
                                    $extra = '';
                                    if ($row['type'] === 'in' && $row['supplier_name'])
                                        $extra = '<span class="badge bg-light text-muted border">dari: ' . htmlspecialchars($row['supplier_name']) . '</span> ';
                                    if ($row['type'] === 'out' && $row['recipient_name'])
                                        $extra = '<span class="badge bg-light text-muted border">ke: ' . htmlspecialchars($row['recipient_name']) . '</span> ';
                                    echo $extra;
                                    echo htmlspecialchars($row['notes'] ?? '');
                                    ?>
                                </td>
                                <td class="text-center">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-info rounded-pill"
                                            onclick="lihatDetail(<?php echo $row['id']; ?>)"
                                            title="Lihat detail lengkap">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top bg-light rounded-bottom-4">
                <small class="text-muted">
                    Menampilkan <?php echo ($offset + 1); ?>–<?php echo min($offset + $limit, $totalRows); ?>
                    dari <?php echo number_format($totalRows); ?> transaksi
                </small>
                <nav>
                    <ul class="pagination pagination-sm mb-0 gap-1">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link rounded-2" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage   = min($totalPages, $page + 2);
                        for ($p = $startPage; $p <= $endPage; $p++): ?>
                            <li class="page-item <?php echo $p === $page ? 'active' : ''; ?>">
                                <a class="page-link rounded-2" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $p])); ?>"><?php echo $p; ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link rounded-2" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 rounded-top-4" id="modalHeaderBg">
                <h5 class="modal-title fw-bold" id="modalDetailLabel">
                    <i class="fas fa-file-alt me-2"></i>Detail Transaksi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="modalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Memuat data...</p>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function lihatDetail(id) {
    const modal     = new bootstrap.Modal(document.getElementById('modalDetail'));
    const body      = document.getElementById('modalBody');
    const header    = document.getElementById('modalHeaderBg');

    body.innerHTML = `<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Memuat...</p></div>`;
    modal.show();

    fetch('<?php echo BASE_URL; ?>/riwayat/detail?id=' + id)
        .then(r => r.json())
        .then(json => {
            if (!json.success) {
                body.innerHTML = `<div class="alert alert-danger">Data tidak ditemukan.</div>`;
                return;
            }
            const d = json.data;
            const typeMap = {
                'in':       { label: 'Barang Masuk',    cls: 'bg-success', icon: 'arrow-circle-down' },
                'out':      { label: 'Barang Keluar',   cls: 'bg-danger',  icon: 'arrow-circle-up' },
                'transfer': { label: 'Pemindahan Barang', cls: 'bg-warning', icon: 'exchange-alt' },
            };
            const t = typeMap[d.type] || { label: d.type, cls: 'bg-secondary', icon: 'question' };
            header.className = `modal-header border-0 rounded-top-4 text-white ${t.cls}`;

            let lokasiHtml = `
                <tr><th>Lokasi Asal</th><td><code>${d.lokasi_asal_kode}</code> — ${d.lokasi_asal_nama}</td></tr>`;
            if (d.type === 'transfer' && d.lokasi_tujuan_kode) {
                lokasiHtml += `<tr><th>Lokasi Tujuan</th><td><code class="text-success">${d.lokasi_tujuan_kode}</code> — ${d.lokasi_tujuan_nama}</td></tr>`;
            }

            let extraHtml = '';
            if (d.type === 'in' && d.supplier_name)
                extraHtml = `<tr><th>Supplier / Asal</th><td>${d.supplier_name}</td></tr>`;
            if (d.type === 'out' && d.recipient_name)
                extraHtml = `<tr><th>Tujuan / Penerima</th><td>${d.recipient_name}</td></tr>`;

            const tgl = new Date(d.transaction_date);
            const fmt = tgl.toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric' })
                      + ' ' + tgl.toLocaleTimeString('id-ID');

            body.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card border-0 bg-light rounded-3 p-3 h-100">
                            <h6 class="fw-bold text-muted mb-3"><i class="fas fa-info-circle me-2"></i>Info Transaksi</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr><th style="width:45%">No. Transaksi</th><td><code class="fw-bold">${d.transaction_number}</code></td></tr>
                                <tr><th>Jenis</th><td><span class="badge ${t.cls} text-${d.type==='transfer'?'dark':'white'}"><i class="fas fa-${t.icon} me-1"></i>${t.label}</span></td></tr>
                                <tr><th>Tanggal</th><td>${fmt}</td></tr>
                                <tr><th>Operator</th><td>${d.full_name ?? d.operator}<br><small class="text-muted">@${d.username}</small></td></tr>
                                ${extraHtml}
                                <tr><th>Keterangan</th><td>${d.notes || '<em class="text-muted">-</em>'}</td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 bg-light rounded-3 p-3 h-100">
                            <h6 class="fw-bold text-muted mb-3"><i class="fas fa-box me-2"></i>Detail Barang & Lokasi</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr><th style="width:45%">Kode Barang</th><td><code>${d.barang_code}</code></td></tr>
                                <tr><th>Nama Barang</th><td>${d.barang_name}</td></tr>
                                <tr><th>Jumlah</th><td><strong class="fs-5">${Number(d.qty).toLocaleString()}</strong> ${d.barang_unit}</td></tr>
                                ${lokasiHtml}
                            </table>
                        </div>
                    </div>
                </div>`;
        })
        .catch(() => {
            body.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Gagal memuat data.</div>`;
        });
}
</script>
