<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0">Detail Barang</h2>
    <div>
        <a href="<?php echo BASE_URL; ?>/barang/edit/<?php echo $barang['id']; ?>" class="btn btn-primary me-2"><i class="fas fa-edit me-2"></i>Edit</a>
        <a href="<?php echo BASE_URL; ?>/barang/index" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold text-primary mb-0"><i class="fas fa-box me-2"></i>Informasi Barang</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="small text-muted fw-semibold text-uppercase mb-1" style="letter-spacing: 1px;">Kode Barang</div>
                        <div class="fw-bold font-monospace fs-5"><?php echo htmlspecialchars($barang['code']); ?></div>
                    </div>
                    <div class="col-md-8">
                        <div class="small text-muted fw-semibold text-uppercase mb-1" style="letter-spacing: 1px;">Nama Barang</div>
                        <div class="fw-bold fs-5"><?php echo htmlspecialchars($barang['name']); ?></div>
                    </div>
                    <div class="col-12"><hr class="my-1"></div>
                    <div class="col-md-4">
                        <div class="small text-muted fw-semibold text-uppercase mb-1" style="letter-spacing: 1px;">Kategori</div>
                        <span class="badge bg-primary bg-opacity-10 text-primary fs-6"><?php echo htmlspecialchars($barang['kategori_name']); ?></span>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted fw-semibold text-uppercase mb-1" style="letter-spacing: 1px;">Satuan</div>
                        <div class="fw-bold"><?php echo strtoupper(htmlspecialchars($barang['unit'])); ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted fw-semibold text-uppercase mb-1" style="letter-spacing: 1px;">Status</div>
                        <?php if ($barang['status'] == 'active'): ?>
                            <span class="badge bg-success rounded-pill px-3">Aktif</span>
                        <?php else: ?>
                            <span class="badge bg-secondary rounded-pill px-3">Nonaktif</span>
                        <?php endif; ?>
                    </div>
                    <div class="col-12"><hr class="my-1"></div>
                    <div class="col-12">
                        <div class="small text-muted fw-semibold text-uppercase mb-1" style="letter-spacing: 1px;">Deskripsi</div>
                        <div class="text-secondary"><?php echo nl2br(htmlspecialchars($barang['description'] ?: '-')); ?></div>
                    </div>
                    <div class="col-12"><hr class="my-1"></div>
                    <div class="col-md-6">
                        <div class="small text-muted fw-semibold text-uppercase mb-1" style="letter-spacing: 1px;">Tanggal Ditambahkan</div>
                        <div><?php echo date('d M Y, H:i', strtotime($barang['created_at'])); ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted fw-semibold text-uppercase mb-1" style="letter-spacing: 1px;">Terakhir Diperbarui</div>
                        <div><?php echo date('d M Y, H:i', strtotime($barang['updated_at'])); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold text-primary mb-0"><i class="fas fa-cubes me-2"></i>Informasi Stok</h6>
            </div>
            <div class="card-body text-center p-4">
                <div class="display-4 fw-bold <?php echo $barang['total_qty'] == 0 ? 'text-danger' : ($barang['total_qty'] <= $barang['min_stock'] ? 'text-warning' : 'text-success'); ?>">
                    <?php echo number_format($barang['total_qty']); ?>
                </div>
                <div class="text-muted mb-3"><?php echo strtoupper($barang['unit']); ?> tersedia</div>
                <div class="d-flex justify-content-between px-3 py-2 bg-light rounded-3">
                    <span class="text-muted small">Minimum Stok</span>
                    <span class="fw-bold text-warning"><?php echo number_format($barang['min_stock']); ?></span>
                </div>
                <?php if ($barang['total_qty'] == 0): ?>
                    <div class="alert alert-danger mt-3 mb-0 py-2 small"><i class="fas fa-times-circle me-1"></i>Stok Habis!</div>
                <?php elseif ($barang['total_qty'] <= $barang['min_stock']): ?>
                    <div class="alert alert-warning mt-3 mb-0 py-2 small"><i class="fas fa-exclamation-triangle me-1"></i>Stok Minimum Tercapai!</div>
                <?php else: ?>
                    <div class="alert alert-success mt-3 mb-0 py-2 small"><i class="fas fa-check-circle me-1"></i>Stok Aman</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="d-grid gap-2">
            <a href="<?php echo BASE_URL; ?>/barang/edit/<?php echo $barang['id']; ?>" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Edit Data Barang
            </a>
            <a href="<?php echo BASE_URL; ?>/barang/delete/<?php echo $barang['id']; ?>" class="btn btn-outline-danger"
               onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini?');">
                <i class="fas fa-trash me-2"></i>Hapus Barang
            </a>
        </div>
    </div>
</div>
