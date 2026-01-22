<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Consumption Rates (Per Student)</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#rateModal">
        <i class="fas fa-plus me-1"></i> Set New Rate
    </button>
</div>

<?php if (session()->getFlashdata('status')) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('status') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Month/Year</th>
                    <th>Qty Per Student</th>
                    <th>Unit</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rates as $rate) : ?>
                    <tr>
                        <td><strong><?= $rate['item_name'] ?></strong></td>
                        <td><span class="badge bg-info text-dark">Class <?= $rate['category'] ?></span></td>
                        <td><?= date("M", mktime(0, 0, 0, $rate['month'], 10)) ?> <?= $rate['year'] ?></td>
                        <td><?= number_format($rate['per_student_qty'], 3) ?></td>
                        <td><?= $rate['unit'] ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary edit-btn" data-id="<?= $rate['id'] ?>"><i class="fas fa-edit"></i></button>
                            <a href="<?= base_url('ItemRates/delete/' . $rate['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="rateModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('ItemRates/store') ?>" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set Consumption Rate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Select Category</label>
                    <select name="category" class="form-select" required>
                        <option value="5-8">Class 5 to 8</option>
                        <option value="8-10">Class 8 to 10</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Select Item</label>
                    <select name="item_id" class="form-select" required>
                        <?php foreach ($items as $item) : ?>
                            <option value="<?= $item['id'] ?>"><?= $item['item_name'] ?> (<?= $item['unit'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Qty per Student</label>
                    <input type="number" step="0.001" name="per_student_qty" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-6"><label>Month</label><select name="month" class="form-select"><?php for ($m = 1; $m <= 12; $m++) : ?><option value="<?= $m ?>"><?= date("F", mktime(0, 0, 0, $m, 10)) ?></option><?php endfor; ?></select></div>
                    <div class="col-6"><label>Year</label><input type="number" name="year" class="form-control" value="<?= date('Y') ?>"></div>
                </div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary w-100">Save Rate</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="editRateModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editRateForm" method="POST" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Consumption Rate</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Category</label>
                    <select name="category" id="edit_category" class="form-select" required>
                        <option value="5-8">Class 5 to 8</option>
                        <option value="8-10">Class 8 to 10</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Item</label>
                    <select name="item_id" id="edit_item_id" class="form-select" required>
                        <?php foreach ($items as $item) : ?>
                            <option value="<?= $item['id'] ?>"><?= $item['item_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Qty per Student</label>
                    <input type="number" step="0.001" name="per_student_qty" id="edit_qty" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-6">
                        <label>Month</label>
                        <select name="month" id="edit_month" class="form-select">
                            <?php for ($m = 1; $m <= 12; $m++) : ?><option value="<?= $m ?>"><?= date("F", mktime(0, 0, 0, $m, 10)) ?></option><?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-6"><label>Year</label><input type="number" name="year" id="edit_year" class="form-control"></div>
                </div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-success w-100">Update Rate</button></div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
    $(document).ready(function() {
        $('.edit-btn').on('click', function() {
            const id = $(this).data('id');
            $.ajax({
                url: '<?= base_url('ItemRates/edit/') ?>/' + id,
                method: 'GET',
                success: function(data) {
                    $('#editRateForm').attr('action', '<?= base_url('ItemRates/update/') ?>/' + id);
                    $('#edit_category').val(data.category);
                    $('#edit_item_id').val(data.item_id);
                    $('#edit_qty').val(data.per_student_qty);
                    $('#edit_month').val(data.month);
                    $('#edit_year').val(data.year);
                    $('#editRateModal').modal('show');
                }
            });
        });
    });
</script>

<?= $this->endSection() ?>