<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0">Master Data Kategori</h2>
    <a href="<?php echo BASE_URL; ?>/kategori/create" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-2"></i>Tambah Kategori</a>
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

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body p-4">
        <form method="GET" action="<?php echo BASE_URL; ?>/kategori/index" class="mb-4">
            <div class="row g-2 align-items-center">
                <div class="col-auto">
                    <label for="search" class="col-form-label fw-bold">Pencarian:</label>
                </div>
                <div class="col-md-4">
                    <input type="text" id="search" name="search" class="form-control" placeholder="Cari nama atau deskripsi..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-secondary"><i class="fas fa-search me-1"></i>Cari</button>
                    <?php if (!empty($search)): ?>
                        <a href="<?php echo BASE_URL; ?>/kategori/index" class="btn btn-outline-danger"><i class="fas fa-times me-1"></i>Reset</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="25%">Nama Kategori</th>
                        <th width="50%">Deskripsi</th>
                        <th width="20%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($categories) > 0): ?>
                        <?php $no = 1; foreach ($categories as $k): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td class="fw-bold text-primary"><?php echo htmlspecialchars($k['name']); ?></td>
                                <td><?php echo htmlspecialchars($k['description']); ?></td>
                                <td class="text-center">
                                    <a href="<?php echo BASE_URL; ?>/kategori/edit/<?php echo $k['id']; ?>" class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="<?php echo BASE_URL; ?>/kategori/delete/<?php echo $k['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');"><i class="fas fa-trash"></i> Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Data kategori tidak ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
