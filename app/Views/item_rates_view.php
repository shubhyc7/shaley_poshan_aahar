<?= $this->extend('main') ?>
<?= $this->section('content') ?>
<title>शालेय पोषण आहार प्रणाली | वापर दर</title>

<div class="card shadow-sm border-0 mb-1">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-primary fw-bold">वापर दर (प्रति विद्यार्थी)</h5>
        <div>
            <a href="<?= base_url('ItemRates/export') ?>" class="btn btn-success me-2">
                <i class="fas fa-file-excel"></i> एक्सेलमध्ये निर्यात करा
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#rateModal">
                <i class="fas fa-plus me-1"></i> नवीन दर सेट करा
            </button>
        </div>
    </div>

    <div class="card-body">

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

        <form method="GET" action="<?= base_url('ItemRates') ?>" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold">महिना निवडा</label>
                <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">सर्व महिने</option>
                    <?php for ($m = 1; $m <= 12; $m++) : ?>
                        <option value="<?= $m ?>" <?= ($filterMonth == $m) ? 'selected' : '' ?>>
                            <?= date("F", mktime(0, 0, 0, $m, 10)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">वर्ष निवडा</label>
                <input type="number" name="year" class="form-control form-control-sm" value="<?= $filterYear ?>" placeholder="उदा. 2024" onchange="this.form.submit()">
            </div>
        </form>
        <div class="table-responsive bg-white rounded shadow-sm border mt-4">
            <table class="table table-bordered table-hover align-middle mb-0">

                <thead class="table-dark">
                    <tr>
                        <th>क्रमांक</th>
                        <th>वस्तू</th>
                        <th>वस्तूचा प्रकार</th>
                        <th>इयत्ता</th>
                        <th>महिना/वर्ष</th>
                        <th>प्रति विद्यार्थी प्रमाण</th>
                        <th>एकक</th>
                        <th>क्रिया</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rates as $rate) : ?>
                        <tr>
                            <td><strong><?= $rate['id'] ?></strong></td>
                            <td><strong><?= $rate['item_name'] ?></strong></td>
                            <td>
                                <span class="badge <?= $rate['item_type'] == 'MAIN' ? 'bg-info' : 'bg-secondary' ?>">
                                    <?= $rate['item_type'] == 'MAIN' ? 'मुख्य' : 'सहाय्यक'; ?>
                                </span>
                            </td>
                            <td><span class="badge bg-info text-dark">इयत्ता <?= $rate['category'] ?></span></td>
                            <td><?= date("M", mktime(0, 0, 0, $rate['month'], 10)) ?> <?= $rate['year'] ?></td>
                            <td><?= number_format($rate['per_student_qty'], 3) ?></td>
                            <td><strong><?= $rate['unit'] ?></strong></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary edit-btn" data-id="<?= $rate['id'] ?>"><i class="fas fa-edit"></i></button>
                                <a href="<?= base_url('ItemRates/delete/' . $rate['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('हटवायचे?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>




<div class="modal fade" id="rateModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('ItemRates/store') ?>" method="POST" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">वापर दर सेट करा</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>इयत्ता निवडा</label>
                    <select name="category" class="form-select" required>
                        <option value="6-8">इयत्ता 6 ते 8</option>
                        <option value="9-10">इयत्ता 9 ते 10</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>वस्तू निवडा</label>
                    <select name="item_id" class="form-select" required>
                        <?php foreach ($items as $item) : ?>
                            <option value="<?= $item['id'] ?>"><?= $item['item_name'] ?> (<?= $item['unit'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>प्रति विद्यार्थी प्रमाण</label>
                    <input type="number" step="0.001" name="per_student_qty" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-6"><label>महिना</label><select name="month" class="form-select"><?php for ($m = 1; $m <= 12; $m++) : ?><option value="<?= $m ?>"><?= date("F", mktime(0, 0, 0, $m, 10)) ?></option><?php endfor; ?></select></div>
                    <div class="col-6"><label>वर्ष</label><input type="number" name="year" class="form-control" value="<?= date('Y') ?>"></div>
                </div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary w-100">जतन करा</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="editRateModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editRateForm" method="POST" class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">वापर दर संपादित करा</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>इयत्ता</label>
                    <select name="category" id="edit_category" class="form-select" required>
                        <option value="6-8">इयत्ता 6 ते 8</option>
                        <option value="9-10">इयत्ता 9 ते 10</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>वस्तू</label>
                    <select name="item_id" id="edit_item_id" class="form-select" required>
                        <?php foreach ($items as $item) : ?>
                            <option value="<?= $item['id'] ?>"><?= $item['item_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>प्रति विद्यार्थी प्रमाण</label>
                    <input type="number" step="0.001" name="per_student_qty" id="edit_qty" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-6">
                        <label>महिना</label>
                        <select name="month" id="edit_month" class="form-select">
                            <?php for ($m = 1; $m <= 12; $m++) : ?><option value="<?= $m ?>"><?= date("F", mktime(0, 0, 0, $m, 10)) ?></option><?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-6"><label>वर्ष</label><input type="number" name="year" id="edit_year" class="form-control"></div>
                </div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-warning w-100">अपडेट करा</button></div>
        </form>
    </div>
</div>

<script src="<?php echo base_url('js/jquery-3.6.0.min.js'); ?>"></script>


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