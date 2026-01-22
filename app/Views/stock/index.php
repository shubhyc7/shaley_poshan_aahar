<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="<?= base_url('Stock') ?>" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold text-uppercase">तारीख निवडा (Select Date)</label>
                <input type="date" name="stock_date" class="form-control" value="<?= $stockDate ?>" onchange="this.form.submit()">
            </div>
            <div class="col-md-8 text-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStockModal">
                    <i class="fas fa-plus"></i> आजचा स्टॉक नोंदवा
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (session()->getFlashdata('status')) : ?>
    <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('status') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= session()->getFlashdata('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="table-responsive bg-white rounded shadow-sm border">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>वस्तूचे नाव</th>
                <th> तारीख</th>
                <th>प्रारंभिक (Opening)</th>
                <th>मिळालेला (Received)</th>
                <th>वापरलेला (Used Today)</th>
                <th class="text-primary">शिल्लक (Closing)</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stock as $row) : ?>
                <tr>
                    <td>
                        <strong><?= $row['item_name'] ?></strong><br>
                    </td>
                    <td>
                        <small class="text-muted"><i class="far fa-calendar-alt"></i> <?= date('d-m-Y', strtotime($row['stock_date'])) ?></small>
                    </td>
                    <td><?= number_format($row['opening_stock'], 3) ?></td>
                    <td class="text-success">+ <?= number_format($row['received_stock'], 3) ?></td>
                    <td class="text-danger">- <?= number_format($row['used_stock'], 3) ?></td>
                    <td class="fw-bold text-primary"><?= number_format($row['remaining_stock'], 3) ?></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-primary edit-stock-btn" data-id="<?= $row['id'] ?>"><i class="fas fa-edit"></i></button>
                        <a href="<?= base_url('Stock/delete/' . $row['id'] . "?stock_date=" . $row['stock_date']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('हटवायचे?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($stock)) : ?>
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">या तारखेसाठी कोणतीही नोंद नाही.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="addStockModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('Stock/store') ?>" method="POST" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">नवीन स्टॉक नोंदवा</h5>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="fw-bold small">तारीख (Stock Date)</label>
                    <input type="date" name="stock_date" class="form-control" value="<?= $stockDate ?>" required>
                </div>
                <div class="mb-3">
                    <label class="fw-bold small">वस्तू निवडा</label>
                    <select name="item_id" class="form-select" id="stock_item_id" required>
                        <option value="">वस्तू निवडा...</option>
                        <?php foreach ($items as $item) : ?>
                            <option value="<?= $item['id'] ?>"><?= $item['item_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="row g-3">
                    <div class="col-6"><label class="small">Opening Stock</label><input type="number" step="0.001" name="opening_stock" class="form-control" required></div>
                    <div class="col-6"><label class="small">Received Today</label><input type="number" step="0.001" name="received_stock" class="form-control" value="0"></div>
                </div>
                <div class="mt-3 bg-light p-2 rounded border">
                    <label class="text-danger small fw-bold">आजचा वापर (Automatically fetched from entries)</label>
                    <input type="number" step="0.001" name="used_stock" id="auto_used" class="form-control bg-white" readonly>
                </div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary w-100">Save Stock</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="editStockModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editStockForm" action="<?= base_url('Stock/store') ?>" method="POST" class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">स्टॉक दुरुस्त करा</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="item_id" id="edit_item_id">

                <div class="mb-3">
                    <label class="fw-bold small">तारीख (Edit Date)</label>
                    <input type="date" name="stock_date" id="edit_date" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="fw-bold small">वस्तू</label>
                    <input type="text" id="edit_item_name" class="form-control bg-light" readonly>
                </div>

                <div class="row g-3">
                    <div class="col-6"><label class="small">Opening</label><input type="number" step="0.001" name="opening_stock" id="edit_opening" class="form-control" required></div>
                    <div class="col-6"><label class="small">Received</label><input type="number" step="0.001" name="received_stock" id="edit_received" class="form-control" required></div>
                </div>

                <div class="mt-3 bg-light p-2 rounded border">
                    <label class="small fw-bold">Used Qty (Calculated for selected date)</label>
                    <input type="number" step="0.001" name="used_stock" id="edit_used" class="form-control bg-white" readonly>
                </div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-warning w-100 text-dark fw-bold">Update Stock</button></div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const usedLookup = <?= json_encode($used_lookup) ?>;

    // Handle used stock preview for new entries
    $('#stock_item_id').on('change', function() {
        $('#auto_used').val(usedLookup[$(this).val()] || 0);
    });

    // Populate and Show Edit Modal
    $('.edit-stock-btn').on('click', function() {
        const id = $(this).data('id');
        $.ajax({
            url: '<?= base_url('Stock/edit/') ?>/' + id,
            method: 'GET',
            success: function(data) {
                $('#edit_id').val(data.id);
                $('#edit_date').val(data.stock_date); // Date is now editable in modal
                $('#edit_item_id').val(data.item_id);
                $('#edit_opening').val(data.opening_stock);
                $('#edit_received').val(data.received_stock);
                $('#edit_used').val(data.used_stock);

                // Get name from the first column of the specific row
                let nameText = $('button[data-id="' + id + '"]').closest('tr').find('td:first strong').text();
                $('#edit_item_name').val(nameText);

                $('#editStockModal').modal('show');
            }
        });
    });
</script>
<?= $this->endSection() ?>