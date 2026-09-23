<?php
// views/transaksi/keluar_form.php
// Variables available: $nomorTransaksi, $tanggal, $barangList
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">
            <i class="fas fa-truck-pickup text-danger me-2"></i>Barang Keluar
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item">Transaksi</li>
                <li class="breadcrumb-item active">Barang Keluar</li>
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
            <div class="card-header bg-danger bg-gradient text-white rounded-top-4 py-3 px-4">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-25 rounded-3 p-2 me-3">
                        <i class="fas fa-dolly fs-5"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Form Transaksi Barang Keluar</h5>
                        <small class="opacity-75">Pastikan stok tersedia sebelum menyimpan</small>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <form id="formBarangKeluar" action="<?php echo BASE_URL; ?>/transaksi/storeKeluar" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo SecurityHelper::csrfToken(); ?>">

                    <!-- Info Transaksi -->
                    <div class="row g-3 mb-4 p-3 bg-light rounded-3 border border-danger border-opacity-10">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small text-uppercase">
                                <i class="fas fa-hashtag me-1"></i>Nomor Transaksi
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-danger text-white border-0"><i class="fas fa-barcode"></i></span>
                                <input type="text"
                                       class="form-control bg-white fw-bold text-danger"
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
                        <i class="fas fa-box-open text-primary me-2"></i>Pilih Barang & Lokasi
                    </h6>

                    <div class="row g-3 mb-3">
                        <!-- Kode Barang -->
                        <div class="col-md-4">
                            <label for="barang_code" class="form-label fw-semibold">
                                Kode Barang <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="text"
                                       class="form-control border-primary"
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

                    <div class="alert alert-warning d-none" id="alertBarangNotFound">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Barang tidak ditemukan atau tidak aktif.
                    </div>

                    <!-- Lokasi & Stok -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label for="lokasi_id" class="form-label fw-semibold">
                                Lokasi Asal <span class="text-danger">*</span>
                            </label>
                            <select class="form-select border-primary" id="lokasi_id" name="lokasi_id" disabled>
                                <option value="">-- Pilih Barang Terlebih Dahulu --</option>
                            </select>
                            <small class="text-muted" id="lokasiHelper">Hanya lokasi yang memiliki stok barang ini yang akan muncul.</small>
                        </div>
                        <div class="col-md-4">
                            <label for="qty" class="form-label fw-semibold">
                                Jumlah Keluar <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number"
                                       class="form-control"
                                       id="qty"
                                       name="qty"
                                       min="1"
                                       placeholder="0"
                                       disabled>
                                <span class="input-group-text bg-light" id="satuanSuffix">-</span>
                            </div>
                            <small class="text-danger d-none fw-bold mt-1" id="stokWarning">Stok tidak cukup!</small>
                        </div>
                    </div>

                    <!-- Penerima -->
                    <div class="mb-3">
                        <label for="recipient_name" class="form-label fw-semibold">
                            Tujuan / Penerima <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-shipping-fast"></i></span>
                            <input type="text"
                                   class="form-control"
                                   id="recipient_name"
                                   name="recipient_name"
                                   placeholder="Nama penerima atau tujuan pengiriman..."
                                   value="<?php echo htmlspecialchars($_POST['recipient_name'] ?? ''); ?>"
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
                                  placeholder="Catatan tambahan untuk pengeluaran ini..."
                                  maxlength="500"><?php echo htmlspecialchars($_POST['keterangan'] ?? ''); ?></textarea>
                    </div>

                    <!-- Submit -->
                    <div class="d-flex gap-2 justify-content-end border-top pt-3 mt-3">
                        <a href="<?php echo BASE_URL; ?>" class="btn btn-light px-4">
                            <i class="fas fa-times me-1"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-danger px-5 fw-semibold" id="btnSubmit" disabled>
                            <i class="fas fa-paper-plane me-2"></i>Proses Barang Keluar
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

    const BASE_URL        = '<?php echo BASE_URL; ?>';
    const barangCodeInput = document.getElementById('barang_code');
    const barangIdInput   = document.getElementById('barang_id');
    const namaBarangInput = document.getElementById('nama_barang');
    const satuanInput     = document.getElementById('satuan');
    const satuanSuffix    = document.getElementById('satuanSuffix');
    const qtyInput        = document.getElementById('qty');
    const lokasiSelect    = document.getElementById('lokasi_id');
    const recipientInput  = document.getElementById('recipient_name');
    const btnCari         = document.getElementById('btnCariBarang');
    const btnSubmit       = document.getElementById('btnSubmit');
    const alertNotFound   = document.getElementById('alertBarangNotFound');
    const stokWarning     = document.getElementById('stokWarning');
    const form            = document.getElementById('formBarangKeluar');

    let searchTimeout = null;
    let locationStocks = {}; // Store stocks: { lokasi_id: qty_available }

    function resetForm() {
        barangIdInput.value   = '';
        namaBarangInput.value = '';
        satuanInput.value     = '';
        satuanSuffix.textContent = '-';
        
        lokasiSelect.innerHTML = '<option value="">-- Pilih Barang Terlebih Dahulu --</option>';
        lokasiSelect.disabled = true;
        
        qtyInput.value = '';
        qtyInput.disabled = true;
        qtyInput.removeAttribute('max');
        
        locationStocks = {};
        stokWarning.classList.add('d-none');
        checkFormValidity();
    }

    function fetchBarang(code) {
        if (!code) { resetForm(); return; }

        // Fetch basic info
        fetch(BASE_URL + '/transaksi/apiBarang?code=' + encodeURIComponent(code))
            .then(r => r.json())
            .then(json => {
                if (json.success) {
                    alertNotFound.classList.add('d-none');
                    barangIdInput.value      = json.data.id;
                    namaBarangInput.value    = json.data.name;
                    satuanInput.value        = json.data.unit;
                    satuanSuffix.textContent = json.data.unit;
                    
                    fetchLokasi(json.data.id);
                } else {
                    resetForm();
                    alertNotFound.classList.remove('d-none');
                }
            })
            .catch(() => resetForm());
    }

    function fetchLokasi(barangId) {
        lokasiSelect.innerHTML = '<option value="">Memuat lokasi...</option>';
        lokasiSelect.disabled = true;
        qtyInput.disabled = true;
        
        fetch(BASE_URL + '/transaksi/apiLokasiStok?barang_id=' + barangId)
            .then(r => r.json())
            .then(json => {
                locationStocks = {};
                if (json.success && json.data.length > 0) {
                    lokasiSelect.innerHTML = '<option value="">-- Pilih Lokasi --</option>';
                    json.data.forEach(loc => {
                        locationStocks[loc.id] = parseInt(loc.qty);
                        lokasiSelect.innerHTML += `<option value="${loc.id}">Lokasi: ${loc.kode} - ${loc.nama} (Sisa Stok: ${loc.qty})</option>`;
                    });
                    lokasiSelect.disabled = false;
                } else {
                    lokasiSelect.innerHTML = '<option value="">-- Stok Tidak Tersedia di Semua Lokasi --</option>';
                }
                checkFormValidity();
            })
            .catch(() => {
                lokasiSelect.innerHTML = '<option value="">Gagal memuat lokasi</option>';
            });
    }

    // Handle Lokasi change
    lokasiSelect.addEventListener('change', function() {
        const locId = this.value;
        if (locId && locationStocks[locId]) {
            qtyInput.disabled = false;
            qtyInput.setAttribute('max', locationStocks[locId]);
            // Re-validate current qty if changed location
            validateQty();
        } else {
            qtyInput.disabled = true;
            qtyInput.value = '';
            stokWarning.classList.add('d-none');
        }
        checkFormValidity();
    });

    // Validate QTY against Max Stock
    function validateQty() {
        const locId = lokasiSelect.value;
        const maxStock = locId ? locationStocks[locId] : 0;
        const currentQty = parseInt(qtyInput.value) || 0;

        if (currentQty > maxStock && maxStock > 0) {
            stokWarning.classList.remove('d-none');
            stokWarning.textContent = `Maksimal stok: ${maxStock}`;
            return false;
        } else {
            stokWarning.classList.add('d-none');
            return true;
        }
    }
    
    qtyInput.addEventListener('input', function() {
        validateQty();
        checkFormValidity();
    });

    function checkFormValidity() {
        const isValid = barangIdInput.value !== '' &&
                        lokasiSelect.value !== '' &&
                        parseInt(qtyInput.value) > 0 &&
                        validateQty() &&
                        recipientInput.value.trim() !== '';
        btnSubmit.disabled = !isValid;
    }

    // Search events
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
        const code = this.value.trim().toUpperCase();
        if (code.length >= 2) {
            searchTimeout = setTimeout(() => fetchBarang(code), 500);
        } else {
            resetForm();
        }
    });

    recipientInput.addEventListener('input', checkFormValidity);

    // Live clock
    function updateClock() {
        const now = new Date();
        const pad = n => String(n).padStart(2, '0');
        document.getElementById('tanggalTransaksi').value =
            pad(now.getDate()) + '/' + pad(now.getMonth()+1) + '/' + now.getFullYear() + ' ' +
            pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
    }
    setInterval(updateClock, 1000);

    // Final form validation
    form.addEventListener('submit', function (e) {
        if (!validateQty()) {
            e.preventDefault();
            alert('Jumlah keluar melebihi batas stok tersedia!');
        }
    });

})();
</script>
