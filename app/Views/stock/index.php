<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-uppercase">Month</label>
                <select name="month" class="form-select">
                    <?php for ($m = 1; $m <= 12; $m++) : ?>
                        <option value="<?= $m ?>" <?= $m == $month ? 'selected' : '' ?>><?= date("F", mktime(0, 0, 0, $m, 10)) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-uppercase">Year</label>
                <input type="number" name="year" class="form-control" value="<?= $year ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100">Filter</button>
            </div>
            <div class="col-md-5 text-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStockModal">
                    <i class="fas fa-plus"></i> Update Stock
                </button>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive bg-white rounded shadow-sm">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Item Name</th>
                <th>Opening</th>
                <th>Received (+)</th>
                <th>Used (-)</th>
                <th class="text-primary">Remaining</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stock as $row) : ?>
                <tr>
                    <td><strong><?= $row['item_name'] ?></strong></td>
                    <td><?= number_format($row['opening_stock'], 3) ?> <?= $row['unit'] ?></td>
                    <td class="text-success">+ <?= number_format($row['received_stock'], 3) ?></td>
                    <td class="text-danger">- <?= number_format($row['used_stock'], 3) ?></td>
                    <td class="fw-bold fs-5"><?= number_format($row['remaining_stock'], 3) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="addStockModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('Stock/store') ?>" method="POST" class="modal-content">
            <div class="modal-header">
                <h5>Update Monthly Stock</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" name="month" value="<?= $month ?>">
                <input type="hidden" name="year" value="<?= $year ?>">
                <div class="mb-3">
                    <label>Item</label>
                    <select name="item_id" class="form-select" id="stock_item_id" required>
                        <option value="">Select Item</option>
                        <?php foreach ($items as $item) : ?>
                            <option value="<?= $item['id'] ?>"><?= $item['item_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row">
                    <div class="col-6 mb-3"><label>Opening Stock</label><input type="number" step="0.001" name="opening_stock" class="form-control" required></div>
                    <div class="col-6 mb-3"><label>Received Stock</label><input type="number" step="0.001" name="received_stock" class="form-control" value="0"></div>
                </div>
                <div class="mb-3">
                    <label>Used Stock (Auto-filled from entries)</label>
                    <input type="number" step="0.001" name="used_stock" id="auto_used" class="form-control bg-light" readonly>
                </div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary w-100">Save Stock Record</button></div>
        </form>
    </div>
</div>

<script>
    const usedLookup = <?= json_encode($used_lookup) ?>;
    $('#stock_item_id').on('change', function() {
        const id = $(this).val();
        $('#auto_used').val(usedLookup[id] || 0);
    });
</script>

<?= $this->endSection() ?>