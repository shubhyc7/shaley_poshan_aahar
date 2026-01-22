<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="<?= base_url('Stock') ?>" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-uppercase">महिना निवडा (Month)</label>
                <select name="month" class="form-select shadow-sm" onchange="this.form.submit()">
                    <?php for ($m = 1; $m <= 12; $m++) : ?>
                        <option value="<?= $m ?>" <?= $m == $month ? 'selected' : '' ?>>
                            <?= date("F", mktime(0, 0, 0, $m, 10)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-bold text-uppercase">वर्ष (Year)</label>
                <select name="year" class="form-select shadow-sm" onchange="this.form.submit()">
                    <?php
                    $currentYear = date('Y');
                    for ($y = $currentYear - 2; $y <= $currentYear + 1; $y++) : ?>
                        <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="col-md-7 text-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStockModal">
                    <i class="fas fa-plus"></i> या महिन्याचा स्टॉक नोंदवा
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (session()->getFlashdata('status')) : ?>
    <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('status') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="table-responsive bg-white rounded shadow-sm border">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>वस्तूचे नाव</th>
                <th>प्रारंभिक (Monthly Opening)</th>
                <th>मिळालेला (Monthly Received)</th>
                <th>वापरलेला (Monthly Used)</th>
                <th class="text-primary">शिल्लक (Closing)</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stock as $row) : ?>
                <tr>
                    <td><strong><?= $row['item_name'] ?></strong></td>
                    <td><?= number_format($row['opening_stock'], 3) ?></td>
                    <td class="text-success">+ <?= number_format($row['received_stock'], 3) ?></td>
                    <td class="text-danger">- <?= number_format($row['used_stock'], 3) ?></td>
                    <td class="fw-bold text-primary"><?= number_format($row['remaining_stock'], 3) ?></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-primary edit-stock-btn" data-id="<?= $row['id'] ?>"><i class="fas fa-edit"></i></button>
                        <a href="<?= base_url('Stock/delete/' . $row['id'] . "?month=$month&year=$year") ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('हटवायचे?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($stock)) : ?>
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">या महिन्यासाठी कोणतीही नोंद नाही.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="addStockModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('Stock/store') ?>" method="POST" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5>नवीन स्टॉक नोंदवा</h5>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="fw-bold small">तारीख</label>
                    <input type="date" name="stock_date" id="add_stock_date" class="form-control fetch-trigger" value="" required>
                </div>
                <div class="mb-3">
                    <label class="fw-bold small">वस्तू निवडा</label>
                    <select name="item_id" class="form-select fetch-trigger" id="add_item_id" required>
                        <option value="">वस्तू निवडा...</option>
                        <?php foreach ($items as $item) : ?>
                            <option value="<?= $item['id'] ?>"><?= $item['item_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="small fw-bold">Opening Stock</label>
                        <input type="number" step="0.001" name="opening_stock" id="add_opening" class="form-control border-primary" required>
                        <small class="text-muted" style="font-size:0.7rem;">मागील शिल्लक</small>
                    </div>
                    <div class="col-6">
                        <label class="small fw-bold">Received Today</label>
                        <input type="number" step="0.001" name="received_stock" class="form-control" value="0">
                    </div>
                </div>
                <div class="mt-3 bg-light p-2 rounded border border-danger-subtle">
                    <label class="text-danger small fw-bold">आजचा वापर (Entries मधून)</label>
                    <input type="number" step="0.001" name="used_stock" id="add_used" class="form-control bg-white" readonly>
                </div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary w-100">स्टॉक जतन करा</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="editStockModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editStockForm" action="<?= base_url('Stock/store') ?>" method="POST" class="modal-content">
            <div class="modal-header bg-warning">
                <h5>स्टॉक दुरुस्त करा</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="edit_id">
                <div class="mb-3">
                    <label class="fw-bold small">तारीख</label>
                    <input type="date" name="stock_date" id="edit_date" class="form-control fetch-trigger-edit" required>
                </div>
                <div class="mb-3">
                    <label class="fw-bold small">वस्तू</label>
                    <select name="item_id" id="edit_item_id" class="form-select bg-light fetch-trigger-edit" readonly style="pointer-events:none;">
                        <?php foreach ($items as $item) : ?>
                            <option value="<?= $item['id'] ?>"><?= $item['item_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-3">
                    <div class="col-6"><label class="small">Opening</label><input type="number" step="0.001" name="opening_stock" id="edit_opening" class="form-control" required></div>
                    <div class="col-6"><label class="small">Received</label><input type="number" step="0.001" name="received_stock" id="edit_received" class="form-control" required></div>
                </div>
                <div class="mt-3 bg-light p-2 rounded border">
                    <label class="small fw-bold">Used Qty</label>
                    <input type="number" step="0.001" name="used_stock" id="edit_used" class="form-control bg-white" readonly>
                </div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-warning w-100 text-dark fw-bold">बदल जतन करा</button></div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Function to fetch Opening and Used stock via AJAX
    function updateStockAutomation(itemId, date, targetOpening, targetUsed) {
        if (!itemId || !date) return;
        $.ajax({
            url: '<?= base_url('Stock/getDynamicValues') ?>',
            method: 'POST',
            data: {
                item_id: itemId,
                stock_date: date
            },
            success: function(res) {
                $(targetOpening).val(res.opening_stock);
                $(targetUsed).val(res.today_usage);
            }
        });
    }

    // ADD MODAL Triggers
    $('.fetch-trigger').on('change', function() {
        updateStockAutomation($('#add_item_id').val(), $('#add_stock_date').val(), '#add_opening', '#add_used');
    });

    // EDIT MODAL Triggers
    $('.fetch-trigger-edit').on('change', function() {
        updateStockAutomation($('#edit_item_id').val(), $('#edit_date').val(), '#edit_opening', '#edit_used');
    });

    // Handle Edit Button Click
    $('.edit-stock-btn').on('click', function() {
        const id = $(this).data('id');
        $.ajax({
            url: '<?= base_url('Stock/edit/') ?>/' + id,
            method: 'GET',
            success: function(data) {
                $('#edit_id').val(data.id);
                $('#edit_date').val(data.stock_date);
                $('#edit_item_id').val(data.item_id);
                $('#edit_opening').val(data.opening_stock);
                $('#edit_received').val(data.received_stock);
                $('#edit_used').val(data.used_stock);
                $('#editStockModal').modal('show');
            }
        });
    });
</script>
<?= $this->endSection() ?>