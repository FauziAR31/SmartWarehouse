        <!-- Sidebar -->
        <div class="bg-dark text-white shadow-sm" id="sidebar-wrapper">
            <div class="sidebar-heading text-center py-4 fs-4 fw-bold border-bottom border-secondary">
                <i class="fas fa-warehouse me-2 text-primary"></i>
                <span class="text-white"><?php echo APP_NAME; ?></span>
            </div>
            <?php
            $currentUrl = $_SERVER['REQUEST_URI'];
            $baseSegment = isset($_GET['url']) ? explode('/', rtrim($_GET['url'], '/')) : ['home'];
            $currentController = strtolower($baseSegment[0] ?? 'home');
            ?>
            <div class="list-group list-group-flush my-3">

                <!-- Dashboard -->
                <a href="<?php echo BASE_URL; ?>" class="list-group-item list-group-item-action bg-transparent text-white fw-semibold <?php echo ($currentController == 'home' || $currentController == '') ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt me-3"></i>Dashboard
                </a>

                <?php if (isset($_SESSION['role_name']) && in_array($_SESSION['role_name'], ['Admin', 'Supervisor'])): ?>
                <!-- Master Data Group -->
                <?php $masterDataActive = in_array($currentController, ['barang', 'kategori', 'lokasi']); ?>
                <div class="list-group-item bg-transparent border-0 px-3 pt-3 pb-1">
                    <small class="text-uppercase text-secondary fw-bold" style="font-size: 0.7rem; letter-spacing: 1.5px;">Master Data</small>
                </div>

                <a href="<?php echo BASE_URL; ?>/kategori/index" class="list-group-item list-group-item-action bg-transparent text-white fw-semibold ps-4 <?php echo ($currentController == 'kategori') ? 'active' : ''; ?>">
                    <i class="fas fa-tags me-3"></i>Kategori
                </a>

                <a href="<?php echo BASE_URL; ?>/barang/index" class="list-group-item list-group-item-action bg-transparent text-white fw-semibold ps-4 <?php echo ($currentController == 'barang') ? 'active' : ''; ?>">
                    <i class="fas fa-box me-3"></i>Barang
                </a>

                <a href="<?php echo BASE_URL; ?>/lokasi/index" class="list-group-item list-group-item-action bg-transparent text-white fw-semibold ps-4 <?php echo ($currentController == 'lokasi') ? 'active' : ''; ?>">
                    <i class="fas fa-map-marker-alt me-3"></i>Lokasi
                </a>
                <?php endif; ?>

                <?php if (isset($_SESSION['role_name']) && in_array($_SESSION['role_name'], ['Admin', 'Supervisor', 'Operator'])): ?>
                <!-- Transaksi Group -->
                <?php $transaksiActive = in_array($currentController, ['transaksi']); ?>
                <div class="list-group-item bg-transparent border-0 px-3 pt-3 pb-1">
                    <small class="text-uppercase text-secondary fw-bold" style="font-size: 0.7rem; letter-spacing: 1.5px;">Transaksi</small>
                </div>
                <a href="<?php echo BASE_URL; ?>/transaksi/masuk" class="list-group-item list-group-item-action bg-transparent text-white fw-semibold ps-4 <?php echo ($currentController == 'transaksi' && ($baseSegment[1] ?? '') == 'masuk') ? 'active' : ''; ?>">
                    <i class="fas fa-arrow-circle-down me-3 text-success"></i>Barang Masuk
                </a>
                <a href="<?php echo BASE_URL; ?>/transaksi/keluar" class="list-group-item list-group-item-action bg-transparent text-white fw-semibold ps-4 <?php echo ($currentController == 'transaksi' && ($baseSegment[1] ?? '') == 'keluar') ? 'active' : ''; ?>">
                    <i class="fas fa-arrow-circle-up me-3 text-danger"></i>Barang Keluar
                </a>
                <a href="<?php echo BASE_URL; ?>/transaksi/transfer" class="list-group-item list-group-item-action bg-transparent text-white fw-semibold ps-4 <?php echo ($currentController == 'transaksi' && ($baseSegment[1] ?? '') == 'transfer') ? 'active' : ''; ?>">
                    <i class="fas fa-exchange-alt me-3 text-warning"></i>Pemindahan Barang
                </a>
                <?php endif; ?>


                <?php if (isset($_SESSION['role_name']) && in_array($_SESSION['role_name'], ['Admin', 'Supervisor'])): ?>
                <!-- Inventory Group -->
                <div class="list-group-item bg-transparent border-0 px-3 pt-3 pb-1">
                    <small class="text-uppercase text-secondary fw-bold" style="font-size: 0.7rem; letter-spacing: 1.5px;">Inventory</small>
                </div>
                <a href="<?php echo BASE_URL; ?>/stok/index" class="list-group-item list-group-item-action bg-transparent text-white fw-semibold ps-4 <?php echo ($currentController == 'stok') ? 'active' : ''; ?>">
                    <i class="fas fa-cubes me-3 text-info"></i>Stok Barang
                </a>
                <a href="<?php echo BASE_URL; ?>/riwayat/index" class="list-group-item list-group-item-action bg-transparent text-white fw-semibold ps-4 <?php echo ($currentController == 'riwayat') ? 'active' : ''; ?>">
                    <i class="fas fa-history me-3 text-primary"></i>Riwayat Transaksi
                </a>
                <?php endif; ?>

                <?php if (isset($_SESSION['role_name']) && in_array($_SESSION['role_name'], ['Admin', 'Supervisor'])): ?>
                <!-- Laporan Group -->
                <div class="list-group-item bg-transparent border-0 px-3 pt-3 pb-1">
                    <small class="text-uppercase text-secondary fw-bold" style="font-size: 0.7rem; letter-spacing: 1.5px;">Laporan</small>
                </div>
                <a href="<?php echo BASE_URL; ?>/laporan/stok" class="list-group-item list-group-item-action bg-transparent text-white fw-semibold ps-4 <?php echo ($currentController == 'laporan' && ($baseSegment[1] ?? '') == 'stok') ? 'active' : ''; ?>">
                    <i class="fas fa-layer-group me-3 text-info"></i>Laporan Stok
                </a>
                <a href="<?php echo BASE_URL; ?>/laporan/masuk" class="list-group-item list-group-item-action bg-transparent text-white fw-semibold ps-4 <?php echo ($currentController == 'laporan' && ($baseSegment[1] ?? '') == 'masuk') ? 'active' : ''; ?>">
                    <i class="fas fa-arrow-circle-down me-3 text-success"></i>Laporan Masuk
                </a>
                <a href="<?php echo BASE_URL; ?>/laporan/keluar" class="list-group-item list-group-item-action bg-transparent text-white fw-semibold ps-4 <?php echo ($currentController == 'laporan' && ($baseSegment[1] ?? '') == 'keluar') ? 'active' : ''; ?>">
                    <i class="fas fa-arrow-circle-up me-3 text-danger"></i>Laporan Keluar
                </a>
                <a href="<?php echo BASE_URL; ?>/laporan/pemindahan" class="list-group-item list-group-item-action bg-transparent text-white fw-semibold ps-4 <?php echo ($currentController == 'laporan' && ($baseSegment[1] ?? '') == 'pemindahan') ? 'active' : ''; ?>">
                    <i class="fas fa-exchange-alt me-3 text-warning"></i>Laporan Pemindahan
                </a>
                <a href="<?php echo BASE_URL; ?>/laporan/aktivitas" class="list-group-item list-group-item-action bg-transparent text-white fw-semibold ps-4 <?php echo ($currentController == 'laporan' && ($baseSegment[1] ?? '') == 'aktivitas') ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-list me-3 text-secondary"></i>Laporan Aktivitas
                </a>
                <a href="<?php echo BASE_URL; ?>/laporan/ai_summary" class="list-group-item list-group-item-action bg-transparent text-white fw-semibold ps-4 <?php echo ($currentController == 'laporan' && ($baseSegment[1] ?? '') == 'ai_summary') ? 'active' : ''; ?>">
                    <i class="fas fa-robot me-3 text-primary"></i>Laporan AI Harian
                </a>
                <?php endif; ?>

                <?php if (isset($_SESSION['role_name']) && in_array($_SESSION['role_name'], ['Admin'])): ?>
                <!-- Pengaturan Group -->
                <div class="list-group-item bg-transparent border-0 px-3 pt-3 pb-1">
                    <small class="text-uppercase text-secondary fw-bold" style="font-size: 0.7rem; letter-spacing: 1.5px;">Pengaturan</small>
                </div>
                <a href="<?php echo BASE_URL; ?>/user/index" class="list-group-item list-group-item-action bg-transparent text-white fw-semibold ps-4 <?php echo ($currentController == 'user') ? 'active' : ''; ?>">
                    <i class="fas fa-users-cog me-3"></i>Manajemen User
                </a>
                <?php endif; ?>

            </div>
        </div>
        <!-- /#sidebar-wrapper -->


        <!-- Page Content -->
        <div id="page-content-wrapper" class="w-100 bg-light">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm px-3">
                <div class="container-fluid">
                    <button class="btn btn-primary btn-sm" id="menu-toggle"><i class="fas fa-bars"></i></button>

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mt-2 mt-lg-0 align-items-center">
                            <li class="nav-item me-3">
                                <a class="nav-link position-relative" href="#">
                                    <i class="fas fa-bell"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                        <span class="visually-hidden">New alerts</span>
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle fw-bold" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['full_name'] ?? 'User'); ?>&background=0D8ABC&color=fff" class="rounded-circle me-1" width="30" height="30" alt="Avatar">
                                    <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="navbarDropdown">
                                    <div class="dropdown-header border-bottom mb-2">
                                        <div class="fw-bold"><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></div>
                                        <small class="text-muted"><i class="fas fa-shield-alt me-1"></i><?php echo htmlspecialchars($_SESSION['role_name'] ?? ''); ?></small>
                                    </div>
                                    <a class="dropdown-item" href="#"><i class="fas fa-user-cog me-2"></i>Profile</a>
                                    <a class="dropdown-item" href="#"><i class="fas fa-cogs me-2"></i>Settings</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>/auth/logout"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <div class="container-fluid px-4 py-4">
