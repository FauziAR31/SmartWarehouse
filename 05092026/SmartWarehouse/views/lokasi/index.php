<?php
// Status helpers
$statusConfig = [
    'kosong'      => ['label' => 'Kosong',     'class' => 'success',   'icon' => 'circle'],
    'terisi'      => ['label' => 'Terisi',     'class' => 'primary',   'icon' => 'box'],
    'tidak_aktif' => ['label' => 'Tidak Aktif','class' => 'secondary', 'icon' => 'ban'],
];
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Master Lokasi</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Master Data Lokasi</h2>
        <small class="text-muted">Kelola posisi rak, level, dan slot penyimpanan barang di gudang</small>
    </div>
    <a href="<?php echo BASE_URL; ?>/lokasi/create" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus me-2"></i>Tambah Lokasi
    </a>
</div>

<!-- Flash Messages -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0D8ABC !important; border-radius: 12px;">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size:0.72rem;letter-spacing:1px;">Total Lokasi</div>
                        <div class="fs-2 fw-bold text-primary"><?php echo $stats['total'] ?? 0; ?></div>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-3 p-2">
                        <i class="fas fa-map-marker-alt text-primary fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #198754 !important; border-radius: 12px;">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size:0.72rem;letter-spacing:1px;">Kosong</div>
                        <div class="fs-2 fw-bold text-success"><?php echo $stats['kosong'] ?? 0; ?></div>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-3 p-2">
                        <i class="fas fa-circle text-success fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0d6efd !important; border-radius: 12px;">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size:0.72rem;letter-spacing:1px;">Terisi</div>
                        <div class="fs-2 fw-bold text-primary"><?php echo $stats['terisi'] ?? 0; ?></div>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-3 p-2">
                        <i class="fas fa-box text-primary fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #6c757d !important; border-radius: 12px;">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size:0.72rem;letter-spacing:1px;">Tidak Aktif</div>
                        <div class="fs-2 fw-bold text-secondary"><?php echo $stats['tidak_aktif'] ?? 0; ?></div>
                    </div>
                    <div class="bg-secondary bg-opacity-10 rounded-3 p-2">
                        <i class="fas fa-ban text-secondary fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Card -->
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-4">

        <!-- Search & Filter -->
        <form method="GET" action="<?php echo BASE_URL; ?>/lokasi/index" id="filterForm" class="mb-4">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold small mb-1">Cari Lokasi</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" 
                               placeholder="Kode atau nama lokasi..."
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small mb-1">Filter Rack</label>
                    <select name="rack" class="form-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Semua Rack</option>
                        <?php foreach ($racks as $r): ?>
                            <option value="<?php echo htmlspecialchars($r); ?>" <?php echo ($rackFilter === $r) ? 'selected' : ''; ?>>
                                Rack <?php echo htmlspecialchars($r); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <?php if (!empty($search) || !empty($rackFilter)): ?>
                        <a href="<?php echo BASE_URL; ?>/lokasi/index" class="btn btn-outline-danger">
                            <i class="fas fa-times me-1"></i>Reset
                        </a>
                    <?php endif; ?>
                </div>
                <div class="col-auto ms-auto">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnViewTable" title="Tampilan Tabel" onclick="switchView('table')">
                            <i class="fas fa-table"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnViewCard" title="Tampilan Kartu" onclick="switchView('card')">
                            <i class="fas fa-th-large"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- ===================== TABLE VIEW ===================== -->
        <div id="viewTable">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="10%">Kode</th>
                            <th width="6%" class="text-center">Rack</th>
                            <th width="6%" class="text-center">Level</th>
                            <th width="6%" class="text-center">Posisi</th>
                            <th width="18%">Nama Lokasi</th>
                            <th width="24%">Barang Tersimpan</th>
                            <th width="8%" class="text-center">Kapasitas</th>
                            <th width="8%" class="text-center">Status</th>
                            <th width="9%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($items) > 0): ?>
                            <?php $no = $offset + 1; foreach ($items as $lokasi): ?>
                                <?php
                                    $sc  = $statusConfig[$lokasi['status']] ?? $statusConfig['kosong'];
                                    $barangList = $itemsByLokasi[$lokasi['id']] ?? [];
                                ?>
                                <tr>
                                    <td class="text-muted"><?php echo $no++; ?></td>
                                    <td>
                                        <span class="badge fs-6 fw-bold font-monospace px-2 py-1"
                                              style="background:linear-gradient(135deg,#0D8ABC,#065f80);letter-spacing:1px;">
                                            <?php echo htmlspecialchars($lokasi['kode']); ?>
                                        </span>
                                    </td>
                                    <td class="text-center fw-bold text-primary"><?php echo htmlspecialchars($lokasi['rack']); ?></td>
                                    <td class="text-center"><?php echo $lokasi['level']; ?></td>
                                    <td class="text-center"><?php echo str_pad($lokasi['posisi'], 2, '0', STR_PAD_LEFT); ?></td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($lokasi['nama']); ?></td>
                                    <td>
                                        <?php if (count($barangList) > 0): ?>
                                            <div class="d-flex flex-column gap-1">
                                                <?php foreach (array_slice($barangList, 0, 3) as $b): ?>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <span class="badge bg-light text-dark border font-monospace" style="font-size:0.68rem;"><?php echo htmlspecialchars($b['code']); ?></span>
                                                        <small class="text-truncate" style="max-width:130px;" title="<?php echo htmlspecialchars($b['name']); ?>">
                                                            <?php echo htmlspecialchars($b['name']); ?>
                                                        </small>
                                                        <span class="badge bg-primary bg-opacity-10 text-primary ms-auto" style="font-size:0.68rem;"><?php echo number_format($b['qty']); ?> <?php echo htmlspecialchars($b['unit']); ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                                <?php if (count($barangList) > 3): ?>
                                                    <small class="text-muted">+<?php echo count($barangList) - 3; ?> barang lainnya</small>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted small fst-italic">— Tidak ada barang —</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center text-muted">
                                        <?php echo $lokasi['kapasitas'] > 0 ? number_format($lokasi['kapasitas']) : '<span class="text-muted">∞</span>'; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-<?php echo $sc['class']; ?> bg-opacity-15 text-<?php echo $sc['class']; ?> border border-<?php echo $sc['class']; ?> border-opacity-25 px-2 py-1">
                                            <i class="fas fa-<?php echo $sc['icon']; ?> me-1" style="font-size:0.65rem;"></i>
                                            <?php echo $sc['label']; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="<?php echo BASE_URL; ?>/lokasi/edit/<?php echo $lokasi['id']; ?>"
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?php echo BASE_URL; ?>/lokasi/delete/<?php echo $lokasi['id']; ?>"
                                               class="btn btn-sm btn-outline-danger" title="Hapus"
                                               onclick="return confirmDelete('<?php echo htmlspecialchars($lokasi['kode']); ?>', <?php echo (int)$lokasi['total_qty']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="fas fa-map-marker-alt fa-3x mb-3 d-block opacity-25"></i>
                                    <?php if (!empty($search) || !empty($rackFilter)): ?>
                                        Tidak ada lokasi yang cocok dengan filter yang dipilih.
                                    <?php else: ?>
                                        Belum ada data lokasi. <a href="<?php echo BASE_URL; ?>/lokasi/create">Tambah sekarang</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ===================== CARD VIEW ===================== -->
        <div id="viewCard" style="display:none;">
            <?php
            // Group by rack for card view
            $byRack = [];
            foreach ($items as $lokasi) {
                $byRack[$lokasi['rack']][] = $lokasi;
            }
            ?>
            <?php foreach ($byRack as $rackName => $lokasiList): ?>
                <div class="mb-4">
                    <h6 class="fw-bold text-uppercase text-muted mb-3 d-flex align-items-center gap-2">
                        <span class="bg-primary text-white rounded px-2 py-1">Rack <?php echo htmlspecialchars($rackName); ?></span>
                        <span class="badge bg-light text-dark border"><?php echo count($lokasiList); ?> lokasi</span>
                    </h6>
                    <div class="row g-3">
                        <?php foreach ($lokasiList as $lokasi): ?>
                            <?php
                                $sc = $statusConfig[$lokasi['status']] ?? $statusConfig['kosong'];
                                $barangList = $itemsByLokasi[$lokasi['id']] ?? [];
                                $borderColor = [
                                    'kosong'      => '#198754',
                                    'terisi'      => '#0d6efd',
                                    'tidak_aktif' => '#6c757d',
                                ][$lokasi['status']] ?? '#dee2e6';
                            ?>
                            <div class="col-sm-6 col-lg-4 col-xl-3">
                                <div class="card border-0 shadow-sm h-100"
                                     style="border-left: 4px solid <?php echo $borderColor; ?> !important; border-radius: 12px; transition: transform 0.2s ease, box-shadow 0.2s ease;"
                                     onmouseenter="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 25px rgba(0,0,0,0.12)'"
                                     onmouseleave="this.style.transform='translateY(0)';this.style.boxShadow=''">
                                    <div class="card-body p-3">
                                        <!-- Header -->
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge fw-bold font-monospace fs-6 px-2 py-1"
                                                  style="background:linear-gradient(135deg,#0D8ABC,#065f80);letter-spacing:1px;">
                                                <?php echo htmlspecialchars($lokasi['kode']); ?>
                                            </span>
                                            <span class="badge rounded-pill bg-<?php echo $sc['class']; ?> bg-opacity-15 text-<?php echo $sc['class']; ?> border border-<?php echo $sc['class']; ?> border-opacity-25" style="font-size:0.7rem;">
                                                <i class="fas fa-<?php echo $sc['icon']; ?> me-1"></i><?php echo $sc['label']; ?>
                                            </span>
                                        </div>

                                        <!-- Nama -->
                                        <div class="fw-semibold mb-1" style="font-size:0.9rem;"><?php echo htmlspecialchars($lokasi['nama']); ?></div>

                                        <!-- Meta -->
                                        <div class="d-flex gap-2 mb-2">
                                            <small class="text-muted"><i class="fas fa-layer-group me-1"></i>Level <?php echo $lokasi['level']; ?></small>
                                            <small class="text-muted"><i class="fas fa-grip-lines me-1"></i>Pos <?php echo str_pad($lokasi['posisi'],2,'0',STR_PAD_LEFT); ?></small>
                                            <?php if ($lokasi['kapasitas'] > 0): ?>
                                                <small class="text-muted"><i class="fas fa-cubes me-1"></i><?php echo number_format($lokasi['kapasitas']); ?></small>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Barang tersimpan -->
                                        <?php if (count($barangList) > 0): ?>
                                            <div class="border-top pt-2 mt-2">
                                                <small class="text-muted fw-semibold d-block mb-1">
                                                    <i class="fas fa-boxes me-1"></i><?php echo count($barangList); ?> jenis barang
                                                    (<?php echo number_format($lokasi['total_qty']); ?> unit)
                                                </small>
                                                <?php foreach (array_slice($barangList, 0, 2) as $b): ?>
                                                    <div class="d-flex justify-content-between align-items-center py-1" style="font-size:0.78rem;">
                                                        <span class="text-truncate" style="max-width:120px;" title="<?php echo htmlspecialchars($b['name']); ?>">
                                                            <?php echo htmlspecialchars($b['name']); ?>
                                                        </span>
                                                        <span class="badge bg-primary bg-opacity-10 text-primary ms-1">
                                                            <?php echo number_format($b['qty']); ?> <?php echo htmlspecialchars($b['unit']); ?>
                                                        </span>
                                                    </div>
                                                <?php endforeach; ?>
                                                <?php if (count($barangList) > 2): ?>
                                                    <small class="text-muted">+<?php echo count($barangList) - 2; ?> lainnya...</small>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="border-top pt-2 mt-2">
                                                <small class="text-muted fst-italic">— Kosong —</small>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Actions -->
                                        <div class="d-flex gap-2 mt-3">
                                            <a href="<?php echo BASE_URL; ?>/lokasi/edit/<?php echo $lokasi['id']; ?>"
                                               class="btn btn-sm btn-outline-primary flex-fill">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </a>
                                            <a href="<?php echo BASE_URL; ?>/lokasi/delete/<?php echo $lokasi['id']; ?>"
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirmDelete('<?php echo htmlspecialchars($lokasi['kode']); ?>', <?php echo (int)$lokasi['total_qty']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($byRack)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-map-marker-alt fa-3x mb-3 d-block opacity-25"></i>
                    Belum ada data lokasi. <a href="<?php echo BASE_URL; ?>/lokasi/create">Tambah sekarang</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
            <small class="text-muted">
                Menampilkan <strong><?php echo count($items); ?></strong> dari <strong><?php echo $totalRows; ?></strong> lokasi
                <?php if (!empty($rackFilter)): ?>
                    di Rack <strong><?php echo htmlspecialchars($rackFilter); ?></strong>
                <?php endif; ?>
            </small>
            <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&rack=<?php echo urlencode($rackFilter); ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&rack=<?php echo urlencode($rackFilter); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&rack=<?php echo urlencode($rackFilter); ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// View switcher (table / card)
function switchView(type) {
    const tableView = document.getElementById('viewTable');
    const cardView  = document.getElementById('viewCard');
    const btnTable  = document.getElementById('btnViewTable');
    const btnCard   = document.getElementById('btnViewCard');

    if (type === 'table') {
        tableView.style.display = '';
        cardView.style.display  = 'none';
        btnTable.classList.add('active');
        btnCard.classList.remove('active');
        localStorage.setItem('lokasiView', 'table');
    } else {
        tableView.style.display = 'none';
        cardView.style.display  = '';
        btnTable.classList.remove('active');
        btnCard.classList.add('active');
        localStorage.setItem('lokasiView', 'card');
    }
}

// Restore last view preference
document.addEventListener('DOMContentLoaded', function() {
    const saved = localStorage.getItem('lokasiView') || 'table';
    switchView(saved);
});

// Delete confirmation
function confirmDelete(kode, totalQty) {
    if (totalQty > 0) {
        alert(`❌ Tidak dapat menghapus lokasi ${kode}!\n\nLokasi ini masih memiliki ${totalQty} unit barang.\nKosongkan stok terlebih dahulu sebelum menghapus.`);
        return false;
    }
    return confirm(`Yakin ingin menghapus lokasi ${kode}?\nTindakan ini tidak dapat dibatalkan.`);
}
</script>
