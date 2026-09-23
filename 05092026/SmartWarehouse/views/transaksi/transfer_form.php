<?php
// views/transaksi/transfer_form.php
// Variables available: $nomorTransaksi, $tanggal, $barangList, $lokasiList
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">
            <i class="fas fa-exchange-alt text-warning me-2"></i>Pemindahan Barang
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item">Transaksi</li>
                <li class="breadcrumb-item active">Pemindahan Barang</li>
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
            <div class="card-header bg-warning bg-gradient text-dark rounded-top-4 py-3 px-4">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-50 rounded-3 p-2 me-3">
                        <i class="fas fa-random fs-5"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Form Transaksi Pemindahan Stok</h5>
                        <small class="opacity-75 text-dark">Pindahkan barang antar lokasi gudang</small>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <form id="formPemindahan" action="<?php echo BASE_URL; ?>/transaksi/storeTransfer" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo SecurityHelper::csrfToken(); ?>">

                    <!-- Info Transaksi -->
                    <div class="row g-3 mb-4 p-3 bg-light rounded-3 border border-warning border-opacity-25">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-muted small text-uppercase">
                                <i class="fas fa-hashtag me-1"></i>Nomor Transaksi
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-warning text-dark border-0"><i class="fas fa-barcode"></i></span>
                                <input type="text"
                                       class="form-control bg-white fw-bold text-dark"
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
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Detail Barang -->
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fas fa-box text-primary me-2"></i>Pilih Barang
                    </h6>

                    <div class="row g-3 mb-4">
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

                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Nama Barang</label>
                            <input type="text" class="form-control bg-light" id="nama_barang" placeholder="Terisi otomatis" readonly>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Satuan</label>
                            <input type="text" class="form-control bg-light" id="satuan" placeholder="Otomatis" readonly>
                        </div>
                    </div>

                    <div class="alert alert-warning d-none mb-4" id="alertBarangNotFound">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Barang tidak ditemukan atau tidak aktif.
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-bold text-dark mb-3">
                        <i class="fas fa-map-marked-alt text-primary me-2"></i>Detail Pemindahan
                    </h6>

                    <div class="row g-3 mb-3">
                        <!-- Lokasi Asal -->
                        <div class="col-md-6">
                            <label for="lokasi_id" class="form-label fw-semibold">
                                Lokasi Asal <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-sign-out-alt"></i></span>
                                <select class="form-select border-danger" id="lokasi_id" name="lokasi_id" disabled>
                                    <option value="">-- Pilih Barang Terlebih Dahulu --</option>
                                </select>
                            </div>
                            <small class="text-muted">Sumber lokasi stok berada.</small>
                        </div>

                        <!-- Lokasi Tujuan -->
                        <div class="col-md-6">
                            <label for="lokasi_tujuan_id" class="form-label fw-semibold">
                                Lokasi Tujuan <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-sign-in-alt"></i></span>
                                <select class="form-select border-success" id="lokasi_tujuan_id" name="lokasi_tujuan_id" disabled>
                                    <option value="">-- Pilih Lokasi Tujuan --</option>
                                    <?php foreach ($lokasiList as $lok): ?>
                                        <?php if ($lok['status'] !== 'tidak_aktif'): ?>
                                            <option value="<?php echo $lok['id']; ?>">
                                                <?php echo htmlspecialchars($lok['kode'] . ' - ' . $lok['nama']); ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <small class="text-danger d-none fw-bold mt-1" id="lokasiWarning">Lokasi asal dan tujuan tidak boleh sama!</small>
                        </div>
                    </div>

                    <!-- Jumlah dipindah -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="qty" class="form-label fw-semibold">
                                Jumlah Dipindahkan <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number"
                                       class="form-control fw-bold text-primary"
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

                    <!-- Keterangan -->
                    <div class="mb-4">
                        <label for="keterangan" class="form-label fw-semibold">Keterangan (Opsional)</label>
                        <textarea class="form-control"
                                  id="keterangan"
                                  name="keterangan"
                                  rows="3"
                                  placeholder="Alasan pemindahan..."
                                  maxlength="500"><?php echo htmlspecialchars($_POST['keterangan'] ?? ''); ?></textarea>
                    </div>

                    <!-- Submit -->
                    <div class="d-flex gap-2 justify-content-end border-top pt-3 mt-3">
                        <a href="<?php echo BASE_URL; ?>" class="btn btn-light px-4">
                            <i class="fas fa-times me-1"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-warning px-5 fw-bold text-dark" id="btnSubmit" disabled>
                            <i class="fas fa-exchange-alt me-2"></i>Proses Pindah Stok
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
    const lokasiAsal      = document.getElementById('lokasi_id');
    const lokasiTujuan    = document.getElementById('lokasi_tujuan_id');
    const btnCari         = document.getElementById('btnCariBarang');
    const btnSubmit       = document.getElementById('btnSubmit');
    const alertNotFound   = document.getElementById('alertBarangNotFound');
    const stokWarning     = document.getElementById('stokWarning');
    const lokasiWarning   = document.getElementById('lokasiWarning');
    const form            = document.getElementById('formPemindahan');

    let searchTimeout = null;
    let locationStocks = {}; 

    function resetForm() {
        barangIdInput.value   = '';
        namaBarangInput.value = '';
        satuanInput.value     = '';
        satuanSuffix.textContent = '-';
        
        lokasiAsal.innerHTML = '<option value="">-- Pilih Barang Terlebih Dahulu --</option>';
        lokasiAsal.disabled = true;
        lokasiTujuan.disabled = true;
        lokasiTujuan.value = "";
        
        qtyInput.value = '';
        qtyInput.disabled = true;
        qtyInput.removeAttribute('max');
        
        locationStocks = {};
        stokWarning.classList.add('d-none');
        lokasiWarning.classList.add('d-none');
        checkFormValidity();
    }

    function fetchBarang(code) {
        if (!code) { resetForm(); return; }

        fetch(BASE_URL + '/transaksi/apiBarang?code=' + encodeURIComponent(code))
            .then(r => r.json())
            .then(json => {
                if (json.success) {
                    alertNotFound.classList.add('d-none');
                    barangIdInput.value      = json.data.id;
                    namaBarangInput.value    = json.data.name;
                    satuanInput.value        = json.data.unit;
                    satuanSuffix.textContent = json.data.unit;
                    
                    fetchLokasiAsal(json.data.id);
                } else {
                    resetForm();
                    alertNotFound.classList.remove('d-none');
                }
            })
            .catch(() => resetForm());
    }

    function fetchLokasiAsal(barangId) {
        lokasiAsal.innerHTML = '<option value="">Memuat lokasi...</option>';
        lokasiAsal.disabled = true;
        lokasiTujuan.disabled = true;
        qtyInput.disabled = true;
        
        fetch(BASE_URL + '/transaksi/apiLokasiStok?barang_id=' + barangId)
            .then(r => r.json())
            .then(json => {
                locationStocks = {};
                if (json.success && json.data.length > 0) {
                    lokasiAsal.innerHTML = '<option value="">-- Pilih Lokasi Asal --</option>';
                    json.data.forEach(loc => {
                        locationStocks[loc.id] = parseInt(loc.qty);
                        lokasiAsal.innerHTML += `<option value="${loc.id}">Lokasi: ${loc.kode} - ${loc.nama} (Stok: ${loc.qty})</option>`;
                    });
                    lokasiAsal.disabled = false;
                    lokasiTujuan.disabled = false;
                } else {
                    lokasiAsal.innerHTML = '<option value="">-- Stok Tidak Tersedia di Semua Lokasi --</option>';
                }
                checkFormValidity();
            })
            .catch(() => {
                lokasiAsal.innerHTML = '<option value="">Gagal memuat lokasi</option>';
            });
    }

    lokasiAsal.addEventListener('change', function() {
        const locId = this.value;
        if (locId && locationStocks[locId]) {
            qtyInput.disabled = false;
            qtyInput.setAttribute('max', locationStocks[locId]);
            validateQty();
        } else {
            qtyInput.disabled = true;
            qtyInput.value = '';
            stokWarning.classList.add('d-none');
        }
        validateLocations();
        checkFormValidity();
    });

    lokasiTujuan.addEventListener('change', function() {
        validateLocations();
        checkFormValidity();
    });

    function validateQty() {
        const locId = lokasiAsal.value;
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

    function validateLocations() {
        const asal = lokasiAsal.value;
        const tujuan = lokasiTujuan.value;
        
        // Hide the destination option that matches source
        Array.from(lokasiTujuan.options).forEach(opt => {
            if (opt.value !== "" && opt.value === asal) {
                opt.style.display = 'none';
            } else {
                opt.style.display = '';
            }
        });

        if (asal && tujuan && asal === tujuan) {
            lokasiWarning.classList.remove('d-none');
            return false;
        } else {
            lokasiWarning.classList.add('d-none');
            return true;
        }
    }
    
    qtyInput.addEventListener('input', function() {
        validateQty();
        checkFormValidity();
    });

    function checkFormValidity() {
        const isValid = barangIdInput.value !== '' &&
                        lokasiAsal.value !== '' &&
                        lokasiTujuan.value !== '' &&
                        lokasiAsal.value !== lokasiTujuan.value &&
                        parseInt(qtyInput.value) > 0 &&
                        validateQty();
        btnSubmit.disabled = !isValid;
    }

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

    function updateClock() {
        const now = new Date();
        const pad = n => String(n).padStart(2, '0');
        document.getElementById('tanggalTransaksi').value =
            pad(now.getDate()) + '/' + pad(now.getMonth()+1) + '/' + now.getFullYear() + ' ' +
            pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
    }
    setInterval(updateClock, 1000);

    form.addEventListener('submit', function (e) {
        if (!validateQty() || !validateLocations()) {
            e.preventDefault();
            alert('Periksa kembali data Anda (Stok / Lokasi Asal / Lokasi Tujuan).');
        }
    });

})();
</script>
