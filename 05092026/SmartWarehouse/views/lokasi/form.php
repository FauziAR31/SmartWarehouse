<?php
$isEdit     = isset($lokasi) && !empty($lokasi);
$formTitle  = $isEdit ? "Edit Lokasi" : "Tambah Lokasi";
$formAction = $isEdit
    ? BASE_URL . "/lokasi/update/" . $lokasi['id']
    : BASE_URL . "/lokasi/store";

// Pre-fill values
$valRack     = strtoupper($isEdit ? $lokasi['rack']   : ($_POST['rack']     ?? ''));
$valLevel    = $isEdit ? $lokasi['level']  : ($_POST['level']    ?? 1);
$valPosisi   = $isEdit ? $lokasi['posisi'] : ($_POST['posisi']   ?? 1);
$valNama     = $isEdit ? $lokasi['nama']   : ($_POST['nama']     ?? '');
$valDesk     = $isEdit ? $lokasi['deskripsi'] : ($_POST['deskripsi'] ?? '');
$valKap      = $isEdit ? $lokasi['kapasitas'] : ($_POST['kapasitas'] ?? 0);
$valStatus   = $isEdit ? $lokasi['status'] : ($_POST['status']   ?? 'kosong');

// Generate live kode preview
$previewKode = ($valRack && $valLevel && $valPosisi)
    ? strtoupper($valRack) . '.' . intval($valLevel) . '.' . str_pad(intval($valPosisi), 2, '0', STR_PAD_LEFT)
    : 'X.0.00';

$statusOptions = [
    'kosong'      => ['label' => 'Kosong',     'desc' => 'Lokasi tersedia, belum ada barang',   'color' => 'success'],
    'terisi'      => ['label' => 'Terisi',     'desc' => 'Lokasi sedang digunakan untuk stok',  'color' => 'primary'],
    'tidak_aktif' => ['label' => 'Tidak Aktif','desc' => 'Lokasi tidak digunakan / dinonaktifkan','color' => 'secondary'],
];
?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/lokasi/index">Master Lokasi</a></li>
                <li class="breadcrumb-item active"><?php echo $formTitle; ?></li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-0">
            <i class="fas fa-<?php echo $isEdit ? 'edit' : 'plus-circle'; ?> me-2 text-primary"></i>
            <?php echo $formTitle; ?>
        </h2>
        <small class="text-muted">Format kode: <code class="bg-light px-2 py-1 rounded">Rack.Level.Posisi</code> → contoh: <code>A.1.01</code></small>
    </div>
    <a href="<?php echo BASE_URL; ?>/lokasi/index" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<!-- Flash error -->
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- ===== FORM ===== -->
    <div class="col-lg-8">
        <form method="POST" action="<?php echo $formAction; ?>" id="lokasiForm">
            <input type="hidden" name="csrf_token" value="<?php echo SecurityHelper::csrfToken(); ?>">

            <!-- Kode Lokasi Builder -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-primary text-white px-4 py-3 rounded-top-4">
                    <h6 class="fw-bold mb-0"><i class="fas fa-qrcode me-2"></i>Kode Lokasi</h6>
                    <small class="opacity-75">Masukkan Rack, Level, dan Posisi untuk membentuk kode unik</small>
                </div>
                <div class="card-body p-4">

                    <!-- Live Preview -->
                    <div class="text-center mb-4">
                        <div class="d-inline-flex flex-column align-items-center p-4 rounded-4 border-2 border-primary border-dashed" 
                             style="background: linear-gradient(135deg,rgba(13,138,188,0.05),rgba(13,138,188,0.1)); min-width: 200px;">
                            <div class="text-muted small fw-semibold mb-1 text-uppercase" style="letter-spacing:1px;">Preview Kode</div>
                            <div id="kodePreview" class="fw-bold font-monospace text-primary" style="font-size:2rem;letter-spacing:4px;">
                                <?php echo htmlspecialchars($previewKode); ?>
                            </div>
                            <div class="d-flex gap-3 mt-2">
                                <small id="previewRack"  class="text-muted"><span class="badge bg-light text-dark border">Rack: <?php echo $valRack ?: '?'; ?></span></small>
                                <small id="previewLevel" class="text-muted"><span class="badge bg-light text-dark border">Level: <?php echo $valLevel; ?></span></small>
                                <small id="previewPosisi"class="text-muted"><span class="badge bg-light text-dark border">Posisi: <?php echo str_pad($valPosisi,2,'0',STR_PAD_LEFT); ?></span></small>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Rack -->
                        <div class="col-md-4">
                            <label for="rack" class="form-label fw-semibold">
                                Rack <span class="text-danger">*</span>
                                <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Satu huruf kapital. Contoh: A, B, C, D"></i>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold text-primary">Rack</span>
                                <input type="text"
                                       id="rack"
                                       name="rack"
                                       class="form-control text-uppercase fw-bold text-center fs-5 font-monospace"
                                       maxlength="1"
                                       placeholder="A"
                                       value="<?php echo htmlspecialchars($valRack); ?>"
                                       oninput="this.value=this.value.toUpperCase().replace(/[^A-Z]/g,''); updatePreview()"
                                       required>
                            </div>
                            <div class="form-text">Huruf A–Z (1 karakter)</div>
                        </div>

                        <!-- Level -->
                        <div class="col-md-4">
                            <label for="level" class="form-label fw-semibold">
                                Level <span class="text-danger">*</span>
                                <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Level rak dari atas ke bawah. Contoh: 1, 2, 3"></i>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold text-primary">Lvl</span>
                                <input type="number"
                                       id="level"
                                       name="level"
                                       class="form-control fw-bold text-center fs-5"
                                       min="1"
                                       max="99"
                                       placeholder="1"
                                       value="<?php echo (int)$valLevel ?: 1; ?>"
                                       oninput="updatePreview()"
                                       required>
                            </div>
                            <div class="form-text">Angka 1–99</div>
                        </div>

                        <!-- Posisi -->
                        <div class="col-md-4">
                            <label for="posisi" class="form-label fw-semibold">
                                Posisi <span class="text-danger">*</span>
                                <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip" title="Nomor slot/posisi dalam level. Contoh: 01, 02, 03"></i>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold text-primary">Pos</span>
                                <input type="number"
                                       id="posisi"
                                       name="posisi"
                                       class="form-control fw-bold text-center fs-5"
                                       min="1"
                                       max="99"
                                       placeholder="1"
                                       value="<?php echo (int)$valPosisi ?: 1; ?>"
                                       oninput="updatePreview()"
                                       required>
                            </div>
                            <div class="form-text">Angka 1–99 (ditampilkan 2 digit)</div>
                        </div>
                    </div>

                    <!-- Format Guide -->
                    <div class="alert alert-info border-0 mt-3 py-2 px-3 rounded-3" style="background:rgba(13,110,253,0.06);">
                        <small>
                            <i class="fas fa-lightbulb text-warning me-1"></i>
                            <strong>Contoh kode:</strong>
                            <code class="ms-1">A.1.01</code>,
                            <code>A.1.02</code>,
                            <code>A.2.01</code>,
                            <code>B.1.01</code>,
                            <code>B.2.03</code>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Detail Lokasi -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-transparent border-bottom px-4 py-3">
                    <h6 class="fw-bold mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Detail Lokasi</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">

                        <!-- Nama Lokasi -->
                        <div class="col-12">
                            <label for="nama" class="form-label fw-semibold">
                                Nama Lokasi <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   id="nama"
                                   name="nama"
                                   class="form-control"
                                   maxlength="100"
                                   placeholder="Contoh: Rak A Level 1 Slot 1"
                                   value="<?php echo htmlspecialchars($valNama); ?>"
                                   required>
                        </div>

                        <!-- Kapasitas -->
                        <div class="col-md-5">
                            <label for="kapasitas" class="form-label fw-semibold">Kapasitas (unit)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-cubes text-muted"></i></span>
                                <input type="number"
                                       id="kapasitas"
                                       name="kapasitas"
                                       class="form-control"
                                       min="0"
                                       placeholder="0 = tidak terbatas"
                                       value="<?php echo (int)$valKap; ?>">
                                <span class="input-group-text text-muted">unit</span>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="col-md-7">
                            <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3"
                                      placeholder="Keterangan tambahan lokasi ini..."><?php echo htmlspecialchars($valDesk); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-transparent border-bottom px-4 py-3">
                    <h6 class="fw-bold mb-0"><i class="fas fa-toggle-on me-2 text-primary"></i>Status Lokasi</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <?php foreach ($statusOptions as $val => $opt): ?>
                            <div class="col-md-4">
                                <label class="card border-2 h-100 p-3 cursor-pointer status-option <?php echo $valStatus === $val ? 'border-'.$opt['color'].' bg-'.$opt['color'].' bg-opacity-5' : 'border-light'; ?>"
                                       style="cursor:pointer; border-radius: 10px; transition: all 0.2s ease;"
                                       for="status_<?php echo $val; ?>">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <input type="radio"
                                               class="form-check-input mt-0"
                                               name="status"
                                               id="status_<?php echo $val; ?>"
                                               value="<?php echo $val; ?>"
                                               <?php echo $valStatus === $val ? 'checked' : ''; ?>>
                                        <span class="badge bg-<?php echo $opt['color']; ?> px-2"><?php echo $opt['label']; ?></span>
                                    </div>
                                    <small class="text-muted d-block ms-4"><?php echo $opt['desc']; ?></small>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4 py-2" id="btnSubmit">
                    <i class="fas fa-save me-2"></i><?php echo $isEdit ? 'Simpan Perubahan' : 'Tambah Lokasi'; ?>
                </button>
                <a href="<?php echo BASE_URL; ?>/lokasi/index" class="btn btn-outline-secondary px-4 py-2">
                    <i class="fas fa-times me-2"></i>Batal
                </a>
            </div>
        </form>
    </div>

    <!-- ===== SIDEBAR PANEL ===== -->
    <div class="col-lg-4">

        <!-- Help Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, #1a202c, #2d3748); color: white;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="fas fa-ruler-combined me-2 text-warning"></i>Panduan Format Kode</h6>

                <div class="mb-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-warning text-dark fw-bold px-2 py-1" style="min-width:28px;">A</span>
                        <div>
                            <div class="fw-semibold text-white small">Rack</div>
                            <small class="text-white-50">Identifikasi baris rak (A, B, C, ...)</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-info fw-bold px-2 py-1" style="min-width:28px;">1</span>
                        <div>
                            <div class="fw-semibold text-white small">Level</div>
                            <small class="text-white-50">Tingkat/lapisan rak (1=atas)</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success fw-bold px-2 py-1" style="min-width:28px;">01</span>
                        <div>
                            <div class="fw-semibold text-white small">Posisi</div>
                            <small class="text-white-50">Nomor slot (2 digit: 01, 02, ...)</small>
                        </div>
                    </div>
                </div>

                <div class="border-top border-white border-opacity-10 pt-3">
                    <div class="fw-semibold text-white-75 small mb-2">Contoh:</div>
                    <div class="d-flex flex-column gap-1">
                        <?php
                        $examples = ['A.1.01', 'A.1.02', 'A.2.01', 'B.1.01', 'B.2.03'];
                        foreach ($examples as $ex): ?>
                            <code class="bg-white bg-opacity-10 text-warning px-2 py-1 rounded small d-block" style="letter-spacing:2px;"><?php echo $ex; ?></code>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Existing Kode Preview (edit mode: show current) -->
        <?php if ($isEdit): ?>
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-bottom px-4 py-3">
                <h6 class="fw-bold mb-0 text-muted"><i class="fas fa-boxes me-2"></i>Barang di Lokasi Ini</h6>
            </div>
            <div class="card-body p-3">
                <?php
                $lokasiModelTemp = new Lokasi();
                $barangDiLokasi  = $lokasiModelTemp->getItemsByLokasiId($lokasi['id']);
                ?>
                <?php if (count($barangDiLokasi) > 0): ?>
                    <?php foreach ($barangDiLokasi as $b): ?>
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <div class="fw-semibold" style="font-size:0.85rem;"><?php echo htmlspecialchars($b['name']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($b['kategori']); ?></small>
                            </div>
                            <span class="badge bg-primary bg-opacity-15 text-primary fw-bold">
                                <?php echo number_format($b['qty']); ?> <?php echo htmlspecialchars($b['unit']); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                    <small class="text-muted d-block mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Lokasi dengan stok tidak dapat dihapus.
                    </small>
                <?php else: ?>
                    <div class="text-center py-3 text-muted">
                        <i class="fas fa-box-open fa-2x mb-2 d-block opacity-25"></i>
                        <small>Tidak ada barang di lokasi ini</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(el => new bootstrap.Tooltip(el));

    // Status card radio styling
    document.querySelectorAll('input[name="status"]').forEach(radio => {
        radio.addEventListener('change', updateStatusCards);
    });
});

// Update the live kode preview
function updatePreview() {
    const rack   = (document.getElementById('rack').value || '?').toUpperCase();
    const level  = parseInt(document.getElementById('level').value) || 0;
    const posisi = parseInt(document.getElementById('posisi').value) || 0;

    const kode = level > 0 && posisi > 0 && rack !== '?'
        ? `${rack}.${level}.${String(posisi).padStart(2, '0')}`
        : 'X.0.00';

    document.getElementById('kodePreview').textContent = kode;
    document.getElementById('previewRack').innerHTML   = `<span class="badge bg-light text-dark border">Rack: ${rack}</span>`;
    document.getElementById('previewLevel').innerHTML  = `<span class="badge bg-light text-dark border">Level: ${level || '?'}</span>`;
    document.getElementById('previewPosisi').innerHTML = `<span class="badge bg-light text-dark border">Posisi: ${String(posisi).padStart(2, '0')}</span>`;

    // Color indicator
    const preview = document.getElementById('kodePreview');
    preview.style.color = (kode === 'X.0.00') ? '#dc3545' : '#0D8ABC';
}

// Status card visual feedback
function updateStatusCards() {
    const colorMap = {
        'kosong':      ['#198754', '#d1fae5'],
        'terisi':      ['#0d6efd', '#dbeafe'],
        'tidak_aktif': ['#6c757d', '#f3f4f6'],
    };
    document.querySelectorAll('input[name="status"]').forEach(radio => {
        const label = radio.closest('label');
        if (radio.checked) {
            const [borderColor, bgColor] = colorMap[radio.value] || ['#dee2e6','#fff'];
            label.style.borderColor = borderColor;
            label.style.backgroundColor = bgColor;
        } else {
            label.style.borderColor = '#dee2e6';
            label.style.backgroundColor = '#fff';
        }
    });
}

// Auto-generate nama lokasi
document.getElementById('rack').addEventListener('input', suggestNama);
document.getElementById('level').addEventListener('input', suggestNama);
document.getElementById('posisi').addEventListener('input', suggestNama);

function suggestNama() {
    const rack   = (document.getElementById('rack').value || '').toUpperCase();
    const level  = parseInt(document.getElementById('level').value) || 0;
    const posisi = parseInt(document.getElementById('posisi').value) || 0;
    const namaEl = document.getElementById('nama');

    // Only suggest if nama is empty or was previously auto-generated
    if (rack && level && posisi && (!namaEl.value || namaEl.dataset.autoGenerated === 'true')) {
        namaEl.value = `Rak ${rack} Level ${level} Posisi ${String(posisi).padStart(2,'0')}`;
        namaEl.dataset.autoGenerated = 'true';
    }
    updatePreview();
}

// Mark nama as manually edited
document.getElementById('nama').addEventListener('input', function() {
    this.dataset.autoGenerated = 'false';
});

// Form validation
document.getElementById('lokasiForm').addEventListener('submit', function(e) {
    const rack   = document.getElementById('rack').value.trim();
    const level  = parseInt(document.getElementById('level').value);
    const posisi = parseInt(document.getElementById('posisi').value);
    const nama   = document.getElementById('nama').value.trim();

    if (!rack || !/^[A-Z]$/.test(rack)) {
        e.preventDefault();
        alert('Rack harus berupa 1 huruf kapital (A–Z)!');
        document.getElementById('rack').focus();
        return;
    }
    if (!level || level < 1) {
        e.preventDefault();
        alert('Level harus berupa angka minimal 1!');
        document.getElementById('level').focus();
        return;
    }
    if (!posisi || posisi < 1) {
        e.preventDefault();
        alert('Posisi harus berupa angka minimal 1!');
        document.getElementById('posisi').focus();
        return;
    }
    if (!nama) {
        e.preventDefault();
        alert('Nama Lokasi wajib diisi!');
        document.getElementById('nama').focus();
        return;
    }

    document.getElementById('btnSubmit').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    document.getElementById('btnSubmit').disabled = true;
});
</script>
