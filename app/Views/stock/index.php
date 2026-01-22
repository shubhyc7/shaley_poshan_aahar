<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="<?= base_url('Stock') ?>" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-uppercase">महिना निवडा</label>
                <select name="month" class="form-select">
                    <?php for ($m = 1; $m <= 12; $m++) : ?>
                        <option value="<?= $m ?>" <?= $m == $month ? 'selected' : '' ?>><?= date("F", mktime(0, 0, 0, $m, 10)) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-uppercase">वर्ष निवडा</label>
                <input type="number" name="year" class="form-control" value="<?= $year ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100">फिल्टर</button>
            </div>
            <div class="col-md-5 text-end">
                <a href="<?= base_url('Stock/export?month=' . $month . '&year=' . $year) ?>" class="btn btn-outline-success me-2">
                    <i class="fas fa-file-excel"></i> एक्सेल रिपोर्ट
                </a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStockModal">
                    <i class="fas fa-plus"></i> स्टॉक नोंदवा
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (session()->getFlashdata('status')) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('status') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>


<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="table-responsive bg-white rounded shadow-sm">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>वस्तूचे नाव</th>
                <th>प्रारंभिक (Opening)</th>
                <th>प्राप्त (Received)</th>
                <th>वापरलेले (Used)</th>
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
                        <button type="button" class="btn btn-sm btn-outline-primary edit-stock-btn" data-id="<?= $row['id'] ?>">
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="<?= base_url('Stock/delete/' . $row['id'] . "?month=$month&year=$year") ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('काय तुम्हाला ही नोंद हटवायची आहे?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="addStockModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('Stock/store') ?>" method="POST" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">मासिक स्टॉक अपडेट करा</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">महिना</label>
                        <select name="month" class="form-select bg-light">
                            <?php for ($m = 1; $m <= 12; $m++) : ?>
                                <option value="<?= $m ?>" <?= $m == $month ? 'selected' : '' ?>><?= date("F", mktime(0, 0, 0, $m, 10)) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">वर्ष</label>
                        <input type="number" name="year" class="form-control bg-light" value="<?= $year ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">वस्तू निवडा</label>
                        <select name="item_id" class="form-select" id="stock_item_id" required>
                            <option value="">वस्तू निवडा...</option>
                            <?php foreach ($items as $item) : ?>
                                <option value="<?= $item['id'] ?>"><?= $item['item_name'] ?> (<?= $item['unit'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">प्रारंभिक स्टॉक</label>
                        <input type="number" step="0.001" name="opening_stock" class="form-control" placeholder="0.000" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">मिळालेला स्टॉक</label>
                        <input type="number" step="0.001" name="received_stock" class="form-control" value="0">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold text-danger">वापरलेला स्टॉक (Auto-calculated)</label>
                        <input type="number" step="0.001" name="used_stock" id="auto_used" class="form-control bg-light" readonly>
                        <small class="text-muted">या महिन्याच्या रोजच्या नोंदींनुसार.</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary w-100">स्टॉक अपडेट करा</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editStockModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editStockForm" method="POST" class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">स्टॉक दुरुस्त करा</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="month" id="edit_month">
                <input type="hidden" name="year" id="edit_year">

                <div class="mb-3">
                    <label class="fw-bold">वस्तू</label>
                    <select name="item_id" id="edit_item_id" class="form-select bg-light" readonly style="pointer-events: none;">
                        <?php foreach ($items as $item) : ?>
                            <option value="<?= $item['id'] ?>"><?= $item['item_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="fw-bold">प्रारंभिक स्टॉक</label>
                        <input type="number" step="0.001" name="opening_stock" id="edit_opening" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="fw-bold">मिळालेला स्टॉक</label>
                        <input type="number" step="0.001" name="received_stock" id="edit_received" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="fw-bold">वापरलेला स्टॉक</label>
                        <input type="number" step="0.001" name="used_stock" id="edit_used" class="form-control bg-light" readonly>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning w-100 fw-bold">बदल जतन करा</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    const usedLookup = <?= json_encode($used_lookup) ?>;

    // Logic for ADD Modal
    $('#stock_item_id').on('change', function() {
        const id = $(this).val();
        $('#auto_used').val(usedLookup[id] || 0);
    });

    // Logic for EDIT Modal
    $('.edit-stock-btn').on('click', function() {
        const id = $(this).data('id');
        $.ajax({
            url: '<?= base_url('Stock/edit/') ?>/' + id,
            method: 'GET',
            success: function(data) {
                $('#editStockForm').attr('action', '<?= base_url('Stock/store') ?>'); // store handles update automatically in our logic
                $('#edit_item_id').val(data.item_id);
                $('#edit_opening').val(data.opening_stock);
                $('#edit_received').val(data.received_stock);
                $('#edit_used').val(data.used_stock);
                $('#edit_month').val(data.month);
                $('#edit_year').val(data.year);

                $('#editStockModal').modal('show');
            }
        });
    });
</script>

<?= $this->endSection() ?>