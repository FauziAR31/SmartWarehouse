<?php
// views/transaksi/masuk_form.php
// Variables available: $nomorTransaksi, $tanggal, $barangList, $lokasiList
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">
            <i class="fas fa-truck-loading text-success me-2"></i>Barang Masuk
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item">Transaksi</li>
                <li class="breadcrumb-item active">Barang Masuk</li>
            </ol>
        </nav>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
        <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-success bg-gradient text-white rounded-top-4 py-3 px-4">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-25 rounded-3 p-2 me-3">
                        <i class="fas fa-file-import fs-5"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Form Transaksi Barang Masuk</h5>
                        <small class="opacity-75">Pastikan data telah diperiksa sebelum menyimpan</small>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <form id="formBarangMasuk" action="<?php echo BASE_URL; ?>/transaksi/store" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo SecurityHelper::csrfToken(); ?>">

                    <!-- Info Transaksi -->
                    <div class="row g-3 mb-4 p-3 bg-light rounded-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small text-uppercase">
                                <i class="fas fa-hashtag me-1"></i>Nomor Transaksi
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-success text-white border-0"><i class="fas fa-barcode"></i></span>
                                <input type="text"
                                       class="form-control bg-white fw-bold text-success"
                                       id="nomorTransaksi"
                                       value="<?php echo htmlspecialchars($nomorTransaksi); ?>"
                                       readonly>
                            </div>
                            <small class="text-muted">Dibuat otomatis oleh sistem</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small text-uppercase">
                                <i class="fas fa-clock me-1"></i>Tanggal & Waktu
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-secondary text-white border-0"><i class="fas fa-calendar-alt"></i></span>
                                <input type="text"
                                       class="form-control bg-white"
                                       id="tanggalTransaksi"
                                       value="<?php echo date('d/m/Y H:i:s'); ?>"
                                       readonly>
                            </div>
                            <small class="text-muted">Waktu server otomatis</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small text-uppercase">
                                <i class="fas fa-user me-1"></i>Operator
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white border-0"><i class="fas fa-user-circle"></i></span>
                                <input type="text"
                                       class="form-control bg-white"
                                       value="<?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Unknown'); ?>"
                                       readonly>
                            </div>
                            <small class="text-muted">Berdasarkan akun yang login</small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Detail Barang -->
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fas fa-box text-primary me-2"></i>Detail Barang
                    </h6>

                    <div class="row g-3 mb-3">
                        <!-- Kode Barang -->
                        <div class="col-md-4">
                            <label for="barang_code" class="form-label fw-semibold">
                                Kode Barang <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="text"
                                       class="form-control"
                                       id="barang_code"
                                       placeholder="Ketik kode..."
                                       autocomplete="off"
                                       list="barangCodeList">
                                <button class="btn btn-outline-primary" type="button" id="btnCariBarang">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <datalist id="barangCodeList">
                                <?php foreach ($barangList as $b): ?>
                                    <option value="<?php echo htmlspecialchars($b['code']); ?>"><?php echo htmlspecialchars($b['name']); ?></option>
                                <?php endforeach; ?>
                            </datalist>
                            <input type="hidden" name="barang_id" id="barang_id">
                        </div>

                        <!-- Nama Barang (auto) -->
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Nama Barang</label>
                            <input type="text"
                                   class="form-control bg-light"
                                   id="nama_barang"
                                   placeholder="Terisi otomatis"
                                   readonly>
                        </div>

                        <!-- Satuan (auto) -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Satuan</label>
                            <input type="text"
                                   class="form-control bg-light"
                                   id="satuan"
                                   placeholder="Otomatis"
                                   readonly>
                        </div>
                    </div>

                    <!-- Jumlah & Lokasi -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="qty" class="form-label fw-semibold">
                                Jumlah <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number"
                                       class="form-control"
                                       id="qty"
                                       name="qty"
                                       min="1"
                                       placeholder="0"
                                       value="<?php echo htmlspecialchars($_POST['qty'] ?? ''); ?>">
                                <span class="input-group-text bg-light" id="satuanSuffix">-</span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label for="lokasi_id" class="form-label fw-semibold">
                                Lokasi Penyimpanan <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="lokasi_id" name="lokasi_id">
                                <option value="">-- Pilih Lokasi --</option>
                                <?php foreach ($lokasiList as $lok): ?>
                                    <?php
                                        $disabled = ($lok['status'] === 'tidak_aktif') ? 'disabled' : '';
                                        $badge = '';
                                        if ($lok['status'] === 'kosong') $badge = '🟢';
                                        elseif ($lok['status'] === 'terisi') $badge = '🟡';
                                        else $badge = '🔴';
                                        $selected = (isset($_POST['lokasi_id']) && $_POST['lokasi_id'] == $lok['id']) ? 'selected' : '';
                                    ?>
                                    <option value="<?php echo $lok['id']; ?>" <?php echo "$disabled $selected"; ?>>
                                        <?php echo "$badge {$lok['kode']} - {$lok['nama']} ({$lok['status']})"; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">
                                <span class="text-success">🟢 Kosong</span> &nbsp;
                                <span class="text-warning">🟡 Terisi</span> &nbsp;
                                <span class="text-danger">🔴 Tidak Aktif (tidak bisa dipilih)</span>
                            </small>
                        </div>
                    </div>

                    <!-- Supplier -->
                    <div class="mb-3">
                        <label for="supplier_name" class="form-label fw-semibold">
                            Supplier / Asal Barang <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                            <input type="text"
                                   class="form-control"
                                   id="supplier_name"
                                   name="supplier_name"
                                   placeholder="Nama supplier atau asal barang..."
                                   value="<?php echo htmlspecialchars($_POST['supplier_name'] ?? ''); ?>"
                                   maxlength="255">
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-4">
                        <label for="keterangan" class="form-label fw-semibold">Keterangan (Opsional)</label>
                        <textarea class="form-control"
                                  id="keterangan"
                                  name="keterangan"
                                  rows="3"
                                  placeholder="Catatan tambahan untuk transaksi ini..."
                                  maxlength="500"><?php echo htmlspecialchars($_POST['keterangan'] ?? ''); ?></textarea>
                    </div>

                    <!-- Alert barang tidak ditemukan -->
                    <div class="alert alert-warning d-none" id="alertBarangNotFound">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Barang dengan kode tersebut tidak ditemukan atau tidak aktif.
                    </div>

                    <!-- Submit -->
                    <div class="d-flex gap-2 justify-content-end border-top pt-3 mt-3">
                        <a href="<?php echo BASE_URL; ?>" class="btn btn-light px-4">
                            <i class="fas fa-times me-1"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-success px-5 fw-semibold" id="btnSubmit" disabled>
                            <i class="fas fa-save me-2"></i>Simpan Transaksi
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    const BASE_URL     = '<?php echo BASE_URL; ?>';
    const barangCodeInput = document.getElementById('barang_code');
    const barangIdInput   = document.getElementById('barang_id');
    const namaBarangInput = document.getElementById('nama_barang');
    const satuanInput     = document.getElementById('satuan');
    const satuanSuffix    = document.getElementById('satuanSuffix');
    const qtyInput        = document.getElementById('qty');
    const lokasiSelect    = document.getElementById('lokasi_id');
    const supplierInput   = document.getElementById('supplier_name');
    const btnCari         = document.getElementById('btnCariBarang');
    const btnSubmit       = document.getElementById('btnSubmit');
    const alertNotFound   = document.getElementById('alertBarangNotFound');

    let searchTimeout = null;

    function resetBarangInfo() {
        barangIdInput.value   = '';
        namaBarangInput.value = '';
        satuanInput.value     = '';
        satuanSuffix.textContent = '-';
        checkFormValidity();
    }

    function setBarangInfo(data) {
        barangIdInput.value      = data.id;
        namaBarangInput.value    = data.name;
        satuanInput.value        = data.unit;
        satuanSuffix.textContent = data.unit;
        alertNotFound.classList.add('d-none');
        checkFormValidity();
    }

    function fetchBarang(code) {
        if (!code) { resetBarangInfo(); return; }

        fetch(BASE_URL + '/transaksi/apiBarang?code=' + encodeURIComponent(code))
            .then(r => r.json())
            .then(json => {
                if (json.success) {
                    setBarangInfo(json.data);
                } else {
                    resetBarangInfo();
                    alertNotFound.classList.remove('d-none');
                }
            })
            .catch(() => resetBarangInfo());
    }

    function checkFormValidity() {
        const isValid = barangIdInput.value !== '' &&
                        lokasiSelect.value !== '' &&
                        parseInt(qtyInput.value) > 0 &&
                        supplierInput.value.trim() !== '';
        btnSubmit.disabled = !isValid;
    }

    // Search on blur or button click
    btnCari.addEventListener('click', function () {
        fetchBarang(barangCodeInput.value.trim().toUpperCase());
    });

    barangCodeInput.addEventListener('blur', function () {
        clearTimeout(searchTimeout);
        fetchBarang(this.value.trim().toUpperCase());
    });

    barangCodeInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        alertNotFound.classList.add('d-none');
        resetBarangInfo();
        const code = this.value.trim().toUpperCase();
        if (code.length >= 2) {
            searchTimeout = setTimeout(() => fetchBarang(code), 500);
        }
    });

    // Enable submit button when fields are filled
    [lokasiSelect, qtyInput, supplierInput].forEach(el => {
        el.addEventListener('change', checkFormValidity);
        el.addEventListener('input', checkFormValidity);
    });

    // Update clock every second
    function updateClock() {
        const now = new Date();
        const pad = n => String(n).padStart(2, '0');
        document.getElementById('tanggalTransaksi').value =
            pad(now.getDate()) + '/' + pad(now.getMonth()+1) + '/' + now.getFullYear() + ' ' +
            pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
    }
    setInterval(updateClock, 1000);

    // Submit validation
    document.getElementById('formBarangMasuk').addEventListener('submit', function (e) {
        if (!barangIdInput.value) {
            e.preventDefault();
            alert('Pilih barang terlebih dahulu dan pastikan nama barang sudah terisi.');
        }
    });

})();
</script>
