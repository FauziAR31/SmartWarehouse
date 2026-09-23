<?php
/**
 * Shared Filter Bar untuk semua laporan
 * Variables needed: $filters, $barangList, $kategoriList, $lokasiList, $userList
 * Variables needed: $reportType (string slug), $reportTitle (string)
 * Optional: $showStatus (bool), $showAction (bool)
 */
?>
<div class="card border-0 shadow-sm rounded-4 mb-4 no-print">
    <div class="card-header bg-transparent border-0 py-3 px-4">
        <h6 class="fw-bold mb-0"><i class="fas fa-filter text-primary me-2"></i>Filter Laporan</h6>
    </div>
    <div class="card-body pt-0 px-4 pb-3">
        <form method="GET" action="<?php echo BASE_URL; ?>/laporan/<?php echo $reportType; ?>">
            <div class="row g-2 mb-2">
                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-muted">Tanggal Mulai</label>
                    <input type="date" class="form-control" name="date_from"
                           value="<?php echo htmlspecialchars($filters['date_from'] ?? ''); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-muted">Tanggal Akhir</label>
                    <input type="date" class="form-control" name="date_to"
                           value="<?php echo htmlspecialchars($filters['date_to'] ?? ''); ?>">
                </div>
                <?php if (!($showStatus ?? false) && !($showAction ?? false)): ?>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-muted">Barang</label>
                    <select class="form-select" name="barang_id">
                        <option value="">Semua Barang</option>
                        <?php foreach ($barangList as $b): ?>
                            <option value="<?php echo $b['id']; ?>" <?php echo (($filters['barang_id'] ?? '') == $b['id']) ? 'selected' : ''; ?>>
                                [<?php echo htmlspecialchars($b['code']); ?>] <?php echo htmlspecialchars($b['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-muted">Kategori</label>
                    <select class="form-select" name="kategori_id">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($kategoriList as $k): ?>
                            <option value="<?php echo $k['id']; ?>" <?php echo (($filters['kategori_id'] ?? '') == $k['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($k['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-muted">Lokasi</label>
                    <select class="form-select" name="lokasi_id">
                        <option value="">Semua Lokasi</option>
                        <?php foreach ($lokasiList as $l): ?>
                            <option value="<?php echo $l['id']; ?>" <?php echo (($filters['lokasi_id'] ?? '') == $l['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($l['kode'] . ' - ' . $l['nama']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <?php if ($showStatus ?? false): ?>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-muted">Kategori</label>
                    <select class="form-select" name="kategori_id">
                        <option value="">Semua</option>
                        <?php foreach ($kategoriList as $k): ?>
                            <option value="<?php echo $k['id']; ?>" <?php echo (($filters['kategori_id'] ?? '') == $k['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($k['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-muted">Lokasi</label>
                    <select class="form-select" name="lokasi_id">
                        <option value="">Semua Lokasi</option>
                        <?php foreach ($lokasiList as $l): ?>
                            <option value="<?php echo $l['id']; ?>" <?php echo (($filters['lokasi_id'] ?? '') == $l['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($l['kode']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-muted">Status</label>
                    <select class="form-select" name="status">
                        <option value="">Semua Status</option>
                        <option value="AMAN"    <?php echo (($filters['status'] ?? '') === 'AMAN')    ? 'selected' : ''; ?>>✅ AMAN</option>
                        <option value="MINIMUM" <?php echo (($filters['status'] ?? '') === 'MINIMUM') ? 'selected' : ''; ?>>⚠️ MINIMUM</option>
                        <option value="HABIS"   <?php echo (($filters['status'] ?? '') === 'HABIS')   ? 'selected' : ''; ?>>❌ HABIS</option>
                    </select>
                </div>
                <?php endif; ?>

                <?php if ($showAction ?? false): ?>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted">Kata Kunci Aksi</label>
                    <input type="text" class="form-control" name="action"
                           placeholder="e.g. BARANG_MASUK, login..."
                           value="<?php echo htmlspecialchars($filters['action'] ?? ''); ?>">
                </div>
                <?php endif; ?>

                <div class="col-md-2">
                    <label class="form-label fw-semibold small text-muted">Operator</label>
                    <select class="form-select" name="user_id">
                        <option value="">Semua Operator</option>
                        <?php foreach ($userList as $u): ?>
                            <option value="<?php echo $u['id']; ?>" <?php echo (($filters['user_id'] ?? '') == $u['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($u['full_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i>Tampilkan
                </button>
                <a href="<?php echo BASE_URL; ?>/laporan/<?php echo $reportType; ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-undo me-1"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>
