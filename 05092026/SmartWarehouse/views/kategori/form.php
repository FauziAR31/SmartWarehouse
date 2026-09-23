<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0"><?php echo isset($kategori) ? 'Edit Kategori' : 'Tambah Kategori'; ?></h2>
    <a href="<?php echo BASE_URL; ?>/kategori/index" class="btn btn-outline-secondary shadow-sm"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body p-4">
        <form method="POST" action="<?php echo BASE_URL; ?>/kategori/<?php echo isset($kategori) ? 'update/'.$kategori['id'] : 'store'; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo SecurityHelper::csrfToken(); ?>">
            <div class="mb-3">
                <label for="name" class="form-label fw-bold">Nama Kategori <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($kategori) ? htmlspecialchars($kategori['name']) : ''; ?>" required>
            </div>
            <div class="mb-4">
                <label for="description" class="form-label fw-bold">Deskripsi</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?php echo isset($kategori) ? htmlspecialchars($kategori['description']) : ''; ?></textarea>
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>
