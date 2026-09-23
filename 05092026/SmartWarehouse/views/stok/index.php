<?php
// views/stok/index.php
// Variables: $stockData, $totalRows, $totalPages, $page, $kategoriList, $lokasiList
// Filters: $search, $kategori_id, $lokasi_id, $status
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">
            <i class="fas fa-cubes text-primary me-2"></i>Stok Barang
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item">Inventory</li>
                <li class="breadcrumb-item active">Stok Barang</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-primary fs-6 px-3 py-2">
            <i class="fas fa-layer-group me-1"></i>Total: <?php echo $totalRows; ?> Barang
        </span>
    </div>
</div>

<!-- Summary Cards -->
<?php
$amanCount = 0; $minCount = 0; $habisCount = 0;
foreach ($stockData as $row) {
    if ($row['total_qty'] == 0) $habisCount++;
    elseif ($row['total_qty'] <= $row['min_stock']) $minCount++;
    else $amanCount++;
}
?>
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 bg-success text-white">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="bg-white bg-opacity-25 rounded-3 p-2"><i class="fas fa-check-circle fs-4"></i></div>
                <div>
                    <div class="fw-bold fs-4"><?php echo $amanCount; ?></div>
                    <small class="opacity-75">Stok Aman</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 bg-warning text-dark">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="bg-white bg-opacity-25 rounded-3 p-2"><i class="fas fa-exclamation-triangle fs-4"></i></div>
                <div>
                    <div class="fw-bold fs-4"><?php echo $minCount; ?></div>
                    <small class="opacity-75 text-dark">Stok Minimum</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm rounded-4 bg-danger text-white">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="bg-white bg-opacity-25 rounded-3 p-2"><i class="fas fa-times-circle fs-4"></i></div>
                <div>
                    <div class="fw-bold fs-4"><?php echo $habisCount; ?></div>
                    <small class="opacity-75">Stok Habis</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="<?php echo BASE_URL; ?>/stok/index" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold small text-muted">Cari Barang</label>
                <div class="input-group">
                    <span class="input-group-text bg-primary text-white border-0"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" name="search" placeholder="Kode atau nama barang..."
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small text-muted">Kategori</label>
                <select class="form-select" name="kategori_id">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($kategoriList as $kat): ?>
                        <option value="<?php echo $kat['id']; ?>" <?php echo ($kategori_id == $kat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($kat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small text-muted">Lokasi</label>
                <select class="form-select" name="lokasi_id">
                    <option value="">Semua Lokasi</option>
                    <?php foreach ($lokasiList as $lok): ?>
                        <option value="<?php echo $lok['id']; ?>" <?php echo ($lokasi_id == $lok['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($lok['kode']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small text-muted">Status</label>
                <select class="form-select" name="status">
                    <option value="">Semua Status</option>
                    <option value="AMAN" <?php echo ($status === 'AMAN') ? 'selected' : ''; ?>>✅ AMAN</option>
                    <option value="MINIMUM" <?php echo ($status === 'MINIMUM') ? 'selected' : ''; ?>>⚠️ MINIMUM</option>
                    <option value="HABIS" <?php echo ($status === 'HABIS') ? 'selected' : ''; ?>>❌ HABIS</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-1"></i>Filter
                </button>
                <a href="<?php echo BASE_URL; ?>/stok/index" class="btn btn-outline-secondary">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="stokTable">
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
                        <th class="text-center">Lokasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($stockData)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-box-open fs-1 mb-3 d-block opacity-25"></i>
                                    <p class="mb-0">Tidak ada data stok ditemukan.</p>
                                    <?php if ($search || $kategori_id || $lokasi_id || $status): ?>
                                        <a href="<?php echo BASE_URL; ?>/stok/index" class="btn btn-sm btn-outline-secondary mt-2">
                                            <i class="fas fa-undo me-1"></i>Reset Filter
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($stockData as $i => $row): ?>
                            <?php
                            $qty = (int)$row['total_qty'];
                            $minStock = (int)$row['min_stock'];
                            if ($qty === 0) {
                                $statusClass = 'danger';
                                $statusText  = 'HABIS';
                                $statusIcon  = 'times-circle';
                            } elseif ($qty <= $minStock) {
                                $statusClass = 'warning';
                                $statusText  = 'MINIMUM';
                                $statusIcon  = 'exclamation-triangle';
                            } else {
                                $statusClass = 'success';
                                $statusText  = 'AMAN';
                                $statusIcon  = 'check-circle';
                            }
                            ?>
                            <tr class="<?php echo ($qty === 0) ? 'table-danger' : (($qty <= $minStock) ? 'table-warning' : ''); ?>">
                                <td class="ps-4 text-muted"><?php echo $offset + $i + 1; ?></td>
                                <td>
                                    <code class="text-primary fw-bold"><?php echo htmlspecialchars($row['code']); ?></code>
                                </td>
                                <td>
                                    <div class="fw-semibold"><?php echo htmlspecialchars($row['name']); ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border">
                                        <?php echo htmlspecialchars($row['kategori_name']); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold fs-5 text-<?php echo $statusClass; ?>">
                                        <?php echo number_format($qty); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="text-muted"><?php echo number_format($minStock); ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border"><?php echo htmlspecialchars($row['unit']); ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-<?php echo $statusClass; ?> px-2 py-1">
                                        <i class="fas fa-<?php echo $statusIcon; ?> me-1"></i>
                                        <?php echo $statusText; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                            onclick="lihatLokasi(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars(addslashes($row['name'])); ?>')"
                                            title="Lihat detail per lokasi">
                                        <i class="fas fa-map-marker-alt me-1"></i>Detail
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
                    dari <?php echo $totalRows; ?> barang
                </small>
                <nav aria-label="Pagination">
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
                            <li class="page-item <?php echo ($p === $page) ? 'active' : ''; ?>">
                                <a class="page-link rounded-2" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $p])); ?>">
                                    <?php echo $p; ?>
                                </a>
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

<!-- Modal Detail Lokasi -->
<div class="modal fade" id="modalDetailLokasi" tabindex="-1" aria-labelledby="modalDetailLokasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-primary text-white rounded-top-4 border-0">
                <h5 class="modal-title fw-bold" id="modalDetailLokasiLabel">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    Detail Stok per Lokasi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="modalBarangName" class="mb-3"></div>
                <div id="modalContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Memuat data...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function lihatLokasi(barangId, barangName) {
    const modal     = new bootstrap.Modal(document.getElementById('modalDetailLokasi'));
    const content   = document.getElementById('modalContent');
    const namDiv    = document.getElementById('modalBarangName');

    namDiv.innerHTML = `<div class="alert alert-primary border-0 py-2">
        <i class="fas fa-box me-2"></i><strong>${barangName}</strong>
    </div>`;

    content.innerHTML = `<div class="text-center py-4">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-2 text-muted">Memuat data lokasi...</p>
    </div>`;
    modal.show();

    fetch('<?php echo BASE_URL; ?>/stok/apiDetailLokasi?barang_id=' + barangId)
        .then(r => r.json())
        .then(json => {
            if (json.success && json.data.length > 0) {
                let rows = json.data.map((loc, i) => `
                    <tr>
                        <td class="ps-3">${i + 1}</td>
                        <td><code class="text-primary fw-bold">${loc.kode}</code></td>
                        <td>${loc.nama}</td>
                        <td class="text-center">
                            <span class="badge bg-success fs-6 px-3">${Number(loc.qty).toLocaleString()}</span>
                        </td>
                    </tr>
                `).join('');

                const totalQty = json.data.reduce((sum, l) => sum + parseInt(l.qty), 0);
                content.innerHTML = `
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th>Kode Lokasi</th>
                                    <th>Nama Lokasi</th>
                                    <th class="text-center">Qty Tersedia</th>
                                </tr>
                            </thead>
                            <tbody>${rows}</tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="3" class="ps-3 text-end">Total Stok Keseluruhan:</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary fs-6 px-3">${totalQty.toLocaleString()}</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>`;
            } else {
                content.innerHTML = `<div class="text-center py-4 text-muted">
                    <i class="fas fa-box-open fs-1 mb-3 d-block opacity-25"></i>
                    <p>Tidak ada stok tersedia di semua lokasi untuk barang ini.</p>
                </div>`;
            }
        })
        .catch(() => {
            content.innerHTML = `<div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>Gagal memuat data. Coba lagi.
            </div>`;
        });
}
</script>
