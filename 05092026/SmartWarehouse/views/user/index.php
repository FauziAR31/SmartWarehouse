<?php
// views/user/index.php
// Variables: $users, $roles
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">
            <i class="fas fa-users-cog text-primary me-2"></i>Manajemen User
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item">Pengaturan</li>
                <li class="breadcrumb-item active">Manajemen User</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
        <i class="fas fa-plus-circle me-1"></i> Tambah User Baru
    </button>
</div>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>User</th>
                        <th>Username & Email</th>
                        <th class="text-center">Role</th>
                        <th class="text-center">Status</th>
                        <th>Terdaftar</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr><td colspan="7" class="text-center py-5 text-muted">Belum ada data user.</td></tr>
                    <?php else: ?>
                        <?php foreach ($users as $i => $u): ?>
                        <tr>
                            <td class="ps-4 text-muted small"><?php echo $i + 1; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($u['full_name']); ?>&background=random" 
                                         class="rounded-circle me-3" width="40" height="40" alt="Avatar">
                                    <div class="fw-bold"><?php echo htmlspecialchars($u['full_name']); ?>
                                        <?php if ($u['id'] == $_SESSION['user_id']) echo '<span class="badge bg-primary ms-1">You</span>'; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div><code class="text-primary">@<?php echo htmlspecialchars($u['username']); ?></code></div>
                                <small class="text-muted"><i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($u['email']); ?></small>
                            </td>
                            <td class="text-center">
                                <?php 
                                $rc = 'secondary';
                                if ($u['role_name'] === 'Admin') $rc = 'danger';
                                if ($u['role_name'] === 'Supervisor') $rc = 'warning text-dark';
                                if ($u['role_name'] === 'Operator') $rc = 'success';
                                ?>
                                <span class="badge bg-<?php echo $rc; ?> px-2 py-1"><?php echo htmlspecialchars($u['role_name']); ?></span>
                            </td>
                            <td class="text-center">
                                <?php if ($u['status'] === 'active'): ?>
                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i>Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><i class="fas fa-ban me-1"></i>Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted">
                                <?php echo date('d M Y', strtotime($u['created_at'])); ?>
                            </td>
                            <td class="text-center pe-4">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill me-1"
                                        onclick='editUser(<?php echo json_encode($u); ?>)' title="Edit Profil">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-warning rounded-pill"
                                        onclick='resetPassword(<?php echo $u['id']; ?>, "<?php echo addslashes($u['full_name']); ?>")' title="Reset Password">
                                    <i class="fas fa-key"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah User -->
<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-labelledby="modalTambahUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?php echo BASE_URL; ?>/user/store" method="POST" class="modal-content border-0 rounded-4 shadow-lg">
            <input type="hidden" name="csrf_token" value="<?php echo SecurityHelper::csrfToken(); ?>">
            <div class="modal-header bg-primary text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-bold" id="modalTambahUserLabel">Tambah User Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Lengkap</label>
                    <input type="text" name="full_name" class="form-control" required placeholder="Contoh: Budi Santoso">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Username</label>
                    <input type="text" name="username" class="form-control" required placeholder="Contoh: budi123">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" required placeholder="Contoh: budi@gudang.com">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Role Akses</label>
                        <select name="role_id" class="form-select" required>
                            <option value="">Pilih Role...</option>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?php echo $r['id']; ?>"><?php echo htmlspecialchars($r['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Status Akun</label>
                        <select name="status" class="form-select" required>
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light rounded-bottom-4">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i>Simpan User</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit User -->
<div class="modal fade" id="modalEditUser" tabindex="-1" aria-labelledby="modalEditUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="<?php echo BASE_URL; ?>/user/update" method="POST" class="modal-content border-0 rounded-4 shadow-lg">
            <input type="hidden" name="csrf_token" value="<?php echo SecurityHelper::csrfToken(); ?>">
            <div class="modal-header bg-warning border-0 rounded-top-4">
                <h5 class="modal-title fw-bold text-dark" id="modalEditUserLabel">Edit Data User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="id" id="edit_id">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Lengkap</label>
                    <input type="text" name="full_name" id="edit_full_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Username</label>
                    <input type="text" name="username" id="edit_username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email" name="email" id="edit_email" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Role Akses</label>
                        <select name="role_id" id="edit_role_id" class="form-select" required>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?php echo $r['id']; ?>"><?php echo htmlspecialchars($r['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Status Akun</label>
                        <select name="status" id="edit_status" class="form-select" required>
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="alert alert-info border-0 mt-2 small">
                    <i class="fas fa-info-circle me-1"></i>Gunakan tombol <strong>Reset Password</strong> di tabel jika ingin mengganti password user ini.
                </div>
            </div>
            <div class="modal-footer border-0 bg-light rounded-bottom-4">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning px-4"><i class="fas fa-save me-2"></i>Update Data</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Reset Password -->
<div class="modal fade" id="modalResetPassword" tabindex="-1" aria-labelledby="modalResetPasswordLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form action="<?php echo BASE_URL; ?>/user/resetPassword" method="POST" class="modal-content border-0 rounded-4 shadow-lg">
            <input type="hidden" name="csrf_token" value="<?php echo SecurityHelper::csrfToken(); ?>">
            <div class="modal-header bg-danger text-white border-0 rounded-top-4">
                <h6 class="modal-title fw-bold" id="modalResetPasswordLabel"><i class="fas fa-key me-2"></i>Reset Password</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <input type="hidden" name="id" id="reset_id">
                <div class="mb-3 text-muted">
                    Setel ulang sandi untuk user<br><strong class="text-dark" id="reset_name_display"></strong>
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label fw-bold small">Password Baru</label>
                    <input type="text" name="new_password" class="form-control text-center fw-bold" required placeholder="Ketik sandi baru...">
                </div>
            </div>
            <div class="modal-footer border-0 p-3 pt-0 justify-content-center">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger"><i class="fas fa-check me-2"></i>Konfirmasi</button>
            </div>
        </form>
    </div>
</div>

<script>
function editUser(u) {
    document.getElementById('edit_id').value = u.id;
    document.getElementById('edit_full_name').value = u.full_name;
    document.getElementById('edit_username').value = u.username;
    document.getElementById('edit_email').value = u.email;
    document.getElementById('edit_role_id').value = u.role_id;
    document.getElementById('edit_status').value = u.status;
    new bootstrap.Modal(document.getElementById('modalEditUser')).show();
}

function resetPassword(id, name) {
    document.getElementById('reset_id').value = id;
    document.getElementById('reset_name_display').textContent = name;
    new bootstrap.Modal(document.getElementById('modalResetPassword')).show();
}
</script>
