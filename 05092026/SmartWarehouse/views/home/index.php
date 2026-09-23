                <div class="d-flex justify-content-between align-items-center mt-2 mb-4">
                    <div>
                        <h2 class="fw-bold text-dark mb-0">Dashboard</h2>
                        <p class="text-secondary mt-1 mb-0">Welcome back, <strong><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong>! (Role: <?php echo htmlspecialchars($_SESSION['role_name']); ?>)</p>
                    </div>
                    <div>
                        <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill"><i class="fas fa-calendar-alt me-2"></i><?php echo date('d M Y'); ?></span>
                    </div>
                </div>
                
                <!-- TOP METRICS -->
                <div class="row g-4 mb-4">
                    <!-- Total Barang -->
                    <div class="col-xl-3 col-lg-6">
                        <div class="card bg-primary text-white h-100 shadow-sm border-0 rounded-4 overflow-hidden position-relative">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="z-1">
                                        <div class="fs-2 fw-bold mb-1"><?php echo number_format($data['total_barang']); ?></div>
                                        <div class="text-white-50 fw-semibold text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem;">Total Macam Barang</div>
                                    </div>
                                    <i class="fas fa-box fa-4x position-absolute" style="right: -10px; bottom: -10px; opacity: 0.15;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Total Stok -->
                    <div class="col-xl-3 col-lg-6">
                        <div class="card bg-info text-white h-100 shadow-sm border-0 rounded-4 overflow-hidden position-relative">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="z-1">
                                        <div class="fs-2 fw-bold mb-1"><?php echo number_format($data['total_stok']); ?></div>
                                        <div class="text-white-50 fw-semibold text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem;">Total Stok Fisik</div>
                                    </div>
                                    <i class="fas fa-boxes fa-4x position-absolute" style="right: -10px; bottom: -10px; opacity: 0.15;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Total Lokasi -->
                    <div class="col-xl-3 col-lg-6">
                        <div class="card bg-secondary text-white h-100 shadow-sm border-0 rounded-4 overflow-hidden position-relative">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="z-1">
                                        <div class="fs-2 fw-bold mb-1"><?php echo number_format($data['total_lokasi']); ?></div>
                                        <div class="text-white-50 fw-semibold text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem;">Titik Lokasi</div>
                                    </div>
                                    <i class="fas fa-map-marker-alt fa-4x position-absolute" style="right: -10px; bottom: -10px; opacity: 0.15;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (in_array($_SESSION['role_name'], ['Admin', 'Supervisor', 'Operator'])): ?>
                <!-- SECOND ROW METRICS -->
                <div class="row g-4 mb-4">
                    <div class="col-xl-3 col-lg-6">
                        <div class="card bg-success text-white h-100 shadow-sm border-0 rounded-4 overflow-hidden position-relative">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="z-1">
                                        <div class="fs-2 fw-bold mb-1">+<?php echo number_format($data['barang_masuk_hari_ini']); ?></div>
                                        <div class="text-white-50 fw-semibold text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem;">Masuk Hari Ini</div>
                                    </div>
                                    <i class="fas fa-arrow-down fa-4x position-absolute" style="right: -10px; bottom: -10px; opacity: 0.15;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-lg-6">
                        <div class="card bg-warning text-white h-100 shadow-sm border-0 rounded-4 overflow-hidden position-relative">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="z-1">
                                        <div class="fs-2 fw-bold mb-1">-<?php echo number_format($data['barang_keluar_hari_ini']); ?></div>
                                        <div class="text-white-50 fw-semibold text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem;">Keluar Hari Ini</div>
                                    </div>
                                    <i class="fas fa-arrow-up fa-4x position-absolute" style="right: -10px; bottom: -10px; opacity: 0.15;"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6">
                        <div class="card border-0 bg-white h-100 shadow-sm rounded-4 border-start border-4 border-warning">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="z-1">
                                        <div class="fs-2 fw-bold text-dark mb-1"><?php echo count($data['stok_minimum']); ?></div>
                                        <div class="text-muted fw-semibold text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem;">Stok Minimum</div>
                                    </div>
                                    <i class="fas fa-exclamation-circle fa-3x text-warning opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6">
                        <div class="card border-0 bg-white h-100 shadow-sm rounded-4 border-start border-4 border-danger">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="z-1">
                                        <div class="fs-2 fw-bold text-dark mb-1"><?php echo count($data['barang_habis']); ?></div>
                                        <div class="text-muted fw-semibold text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem;">Barang Habis</div>
                                    </div>
                                    <i class="fas fa-times-circle fa-3x text-danger opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- CHARTS AND LISTS -->
                <div class="row g-4 mt-2">
                    
                    <?php if (in_array($_SESSION['role_name'], ['Admin', 'Supervisor'])): ?>
                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0 rounded-4 h-100">
                            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 fw-bold text-primary"><i class="fas fa-chart-area me-2"></i>Statistik Barang Masuk & Keluar (7 Hari)</h6>
                            </div>
                            <div class="card-body p-4">
                                <canvas id="inventoryChart" width="100%" height="40"></canvas>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="col-lg-4">
                        <div class="card shadow-sm border-0 rounded-4 h-100">
                            <div class="card-header bg-white border-bottom py-3">
                                <h6 class="m-0 fw-bold text-primary"><i class="fas fa-history me-2"></i>Transaksi Terbaru</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush rounded-bottom-4">
                                    <?php if(count($data['recent_transactions']) > 0): ?>
                                        <?php foreach($data['recent_transactions'] as $trx): ?>
                                        <div class="list-group-item px-4 py-3">
                                            <div class="d-flex w-100 justify-content-between align-items-center">
                                                <h6 class="mb-1 fw-bold <?php echo $trx['type'] == 'in' ? 'text-success' : 'text-warning'; ?>">
                                                    <i class="fas <?php echo $trx['type'] == 'in' ? 'fa-arrow-down' : 'fa-arrow-up'; ?> me-1"></i>
                                                    <?php echo strtoupper($trx['type']); ?> - <?php echo htmlspecialchars($trx['transaction_number']); ?>
                                                </h6>
                                                <small class="text-muted"><?php echo date('d M, H:i', strtotime($trx['transaction_date'])); ?></small>
                                            </div>
                                            <p class="mb-1 text-secondary small">Oleh: <?php echo htmlspecialchars($trx['user_name']); ?></p>
                                        </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="p-4 text-center text-muted">Belum ada transaksi.</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- WARNING LISTS -->
                <div class="row g-4 mt-2">
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 rounded-4 h-100">
                            <div class="card-header bg-warning text-dark border-bottom py-3">
                                <h6 class="m-0 fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Peringatan: Stok Minimum (<= 10)</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-4">Kode</th>
                                                <th>Nama Barang</th>
                                                <th class="text-center">Sisa Stok</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(count($data['stok_minimum']) > 0): ?>
                                                <?php foreach($data['stok_minimum'] as $item): ?>
                                                <tr>
                                                    <td class="ps-4"><span class="badge bg-secondary"><?php echo htmlspecialchars($item['code']); ?></span></td>
                                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                                    <td class="text-center fw-bold text-warning"><?php echo htmlspecialchars($item['total_qty']); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr><td colspan="3" class="text-center py-4 text-muted">Tidak ada barang dengan stok minimum.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 rounded-4 h-100">
                            <div class="card-header bg-danger text-white border-bottom py-3">
                                <h6 class="m-0 fw-bold"><i class="fas fa-times-circle me-2"></i>Peringatan: Barang Habis (0)</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-4">Kode</th>
                                                <th>Nama Barang</th>
                                                <th class="text-center">Sisa Stok</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(count($data['barang_habis']) > 0): ?>
                                                <?php foreach($data['barang_habis'] as $item): ?>
                                                <tr>
                                                    <td class="ps-4"><span class="badge bg-secondary"><?php echo htmlspecialchars($item['code']); ?></span></td>
                                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                                    <td class="text-center fw-bold text-danger">0</td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr><td colspan="3" class="text-center py-4 text-muted">Tidak ada barang yang habis.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Include Chart.js -->
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const chartDataRaw = '<?php echo $data["chart_data"]; ?>';
                        if(document.getElementById('inventoryChart') && chartDataRaw) {
                            const chartData = JSON.parse(chartDataRaw);
                            
                            const ctx = document.getElementById('inventoryChart').getContext('2d');
                            new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: chartData.labels,
                                    datasets: [
                                        {
                                            label: 'Barang Masuk',
                                            data: chartData.in,
                                            borderColor: '#198754',
                                            backgroundColor: 'rgba(25, 135, 84, 0.1)',
                                            borderWidth: 2,
                                            fill: true,
                                            tension: 0.4
                                        },
                                        {
                                            label: 'Barang Keluar',
                                            data: chartData.out,
                                            borderColor: '#ffc107',
                                            backgroundColor: 'rgba(255, 193, 7, 0.1)',
                                            borderWidth: 2,
                                            fill: true,
                                            tension: 0.4
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        legend: {
                                            position: 'top',
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                precision: 0
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    });
                </script>
