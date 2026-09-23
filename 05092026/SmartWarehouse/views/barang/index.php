<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0">Master Data Barang</h2>
    <a href="<?php echo BASE_URL; ?>/barang/create" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-2"></i>Tambah Barang</a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-4">
        <!-- Filter & Search -->
        <form method="GET" action="<?php echo BASE_URL; ?>/barang/index" class="mb-4">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold small">Cari Barang</label>
                    <input type="text" name="search" class="form-control" placeholder="Nama atau kode barang..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small">Filter Kategori</label>
                    <select name="kategori_id" class="form-select">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($kategori_id == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-secondary"><i class="fas fa-search me-1"></i>Cari</button>
                    <?php if (!empty($search) || !empty($kategori_id)): ?>
                        <a href="<?php echo BASE_URL; ?>/barang/index" class="btn btn-outline-danger"><i class="fas fa-times me-1"></i>Reset</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="12%">Kode</th>
                        <th width="22%">Nama Barang</th>
                        <th width="13%">Kategori</th>
                        <th width="8%" class="text-center">Satuan</th>
                        <th width="10%" class="text-center">Stok Saat Ini</th>
                        <th width="8%" class="text-center">Min. Stok</th>
                        <th width="8%" class="text-center">Status</th>
                        <th width="14%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($items) > 0): ?>
                        <?php $no = $offset + 1; foreach ($items as $item): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><span class="badge bg-light text-dark border font-monospace"><?php echo htmlspecialchars($item['code']); ?></span></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary"><?php echo htmlspecialchars($item['kategori_name']); ?></span></td>
                                <td class="text-center"><?php echo htmlspecialchars($item['unit']); ?></td>
                                <td class="text-center fw-bold <?php echo $item['total_qty'] == 0 ? 'text-danger' : ($item['total_qty'] <= $item['min_stock'] ? 'text-warning' : 'text-success'); ?>">
                                    <?php echo number_format($item['total_qty']); ?>
                                </td>
                                <td class="text-center text-muted"><?php echo number_format($item['min_stock']); ?></td>
                                <td class="text-center">
                                    <?php if ($item['status'] == 'active'): ?>
                                        <span class="badge bg-success rounded-pill">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary rounded-pill">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo BASE_URL; ?>/barang/show/<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-info me-1" title="Detail"><i class="fas fa-eye"></i></a>
                                    <a href="<?php echo BASE_URL; ?>/barang/edit/<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="<?php echo BASE_URL; ?>/barang/delete/<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus barang ini?');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fa-3x mb-3 d-block"></i>
                                Data barang tidak ditemukan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination & Info -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted">
                Menampilkan <?php echo count($items); ?> dari <?php echo $totalRows; ?> barang.
            </small>
            <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&kategori_id=<?php echo $kategori_id; ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&kategori_id=<?php echo $kategori_id; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&kategori_id=<?php echo $kategori_id; ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>
