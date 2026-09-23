<?php
/**
 * Shared export toolbar + print styles for all laporan views
 * Variables: $reportTitle, $rows (to show count)
 */
?>
<!-- Export Toolbar -->
<div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <div>
        <span class="badge bg-primary fs-6 px-3 py-2">
            <i class="fas fa-table me-1"></i><?php echo count($rows); ?> Baris Data
        </span>
        <?php if (array_filter($filters)): ?>
            <span class="badge bg-warning text-dark ms-2">
                <i class="fas fa-filter me-1"></i>Filter Aktif
            </span>
        <?php endif; ?>
    </div>
    <div class="d-flex gap-2">
        <button onclick="exportExcel()" class="btn btn-success btn-sm px-3" <?php echo empty($rows) ? 'disabled' : ''; ?>>
            <i class="fas fa-file-excel me-1"></i>Export Excel
        </button>
        <button onclick="exportPDF()" class="btn btn-danger btn-sm px-3" <?php echo empty($rows) ? 'disabled' : ''; ?>>
            <i class="fas fa-file-pdf me-1"></i>Export PDF
        </button>
        <button onclick="window.print()" class="btn btn-secondary btn-sm px-3" <?php echo empty($rows) ? 'disabled' : ''; ?>>
            <i class="fas fa-print me-1"></i>Cetak
        </button>
    </div>
</div>

<!-- Print Header (visible only when printing) -->
<div class="print-only mb-4">
    <div class="text-center">
        <h4 class="fw-bold"><?php echo APP_NAME; ?></h4>
        <h5><?php echo $reportTitle; ?></h5>
        <?php if (!empty($filters['date_from']) || !empty($filters['date_to'])): ?>
            <p class="mb-0">Periode:
                <?php echo $filters['date_from'] ? date('d/m/Y', strtotime($filters['date_from'])) : '...'; ?>
                s/d
                <?php echo $filters['date_to'] ? date('d/m/Y', strtotime($filters['date_to'])) : '...'; ?>
            </p>
        <?php endif; ?>
        <small>Dicetak: <?php echo date('d/m/Y H:i'); ?></small>
        <hr>
    </div>
</div>

<style>
@media print {
    .no-print, #sidebar-wrapper, .navbar, nav[aria-label="breadcrumb"] { display: none !important; }
    .print-only { display: block !important; }
    body { background: white !important; }
    .card { box-shadow: none !important; border: 1px solid #ddd !important; }
    .badge { border: 1px solid #999 !important; }
    #page-content-wrapper { margin: 0 !important; padding: 0 !important; }
    .container-fluid { padding: 0 !important; }
}
@media screen { .print-only { display: none; } }
</style>

<script>
function exportExcel() {
    const table = document.getElementById('laporanTable');
    if (!table) return;

    // Build CSV
    let csv = [];
    const title = <?php echo json_encode($reportTitle); ?>;
    csv.push(title);
    csv.push('Dicetak: ' + new Date().toLocaleString('id-ID'));
    csv.push('');

    const rows = table.querySelectorAll('tr');
    rows.forEach(row => {
        const cols = row.querySelectorAll('th, td');
        let rowData = [];
        cols.forEach(col => {
            let text = col.innerText.replace(/\n/g, ' ').replace(/,/g, ';').trim();
            rowData.push('"' + text + '"');
        });
        csv.push(rowData.join(','));
    });

    const blob = new Blob(['\uFEFF' + csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = title.replace(/\s+/g, '_') + '_' + new Date().toISOString().slice(0,10) + '.csv';
    link.click();
}

function exportPDF() {
    window.print();
}
</script>
