<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>विद्यार्थी संख्या मास्टर</h2>
    <div>
        <a href="<?= base_url('StudentStrength/export') ?>" class="btn btn-success me-2">
            <i class="fas fa-file-excel"></i> एक्सेलमध्ये निर्यात करा
        </a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus"></i> नवीन संख्या जोडा
        </button>
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

<div class="card shadow-sm border-0">
    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>क्रमांक</th>
                    <th>श्रेणी</th>
                    <th>महिना / वर्ष</th>
                    <th>एकूण विद्यार्थी</th>
                    <th>क्रिया</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $row) : ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td>
                            <span class="badge <?= $row['category'] == '5-8' ? 'bg-info' : 'bg-primary' ?>">
                                इयत्ता <?= $row['category'] ?>
                            </span>
                        </td>
                        <td><?= date("F", mktime(0, 0, 0, $row['month'], 10)) ?> - <?= $row['year'] ?></td>
                        <td><strong><?= $row['total_students'] ?></strong></td>
                        <td>
                            <button type="button" class="btn btn-primary btn-sm edit-btn" data-id="<?= $row['id'] ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="<?= base_url('StudentStrength/delete/' . $row['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('हटवायचे?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('StudentStrength/store') ?>" method="POST" class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">विद्यार्थी संख्या जोडा</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">श्रेणी निवडा</label>
                    <select name="category" class="form-select" required>
                        <option value="5-8">इयत्ता 5 ते 8</option>
                        <option value="8-10">इयत्ता 8 ते 10</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">एकूण विद्यार्थी</label>
                    <input type="number" name="total_students" class="form-control" placeholder="विद्यार्थ्यांची संख्या प्रविष्ट करा" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">महिना</label>
                        <select name="month" class="form-select" required>
                            <?php for ($m = 1; $m <= 12; $m++) : ?>
                                <option value="<?= $m ?>" <?= $m == date('n') ? 'selected' : '' ?>>
                                    <?= date("F", mktime(0, 0, 0, $m, 10)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">वर्ष</label>
                        <input type="number" name="year" class="form-control" value="<?= date('Y') ?>" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">बंद करा</button>
                <button type="submit" class="btn btn-success px-4">जतन करा</button>
            </div>
        </form>
    </div>
</div>



<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editForm" method="POST" class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">विद्यार्थी संख्या संपादित करा</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="fw-bold">श्रेणी</label>
                    <select name="category" id="edit_category" class="form-select" required>
                        <option value="5-8">इयत्ता 5 ते 8</option>
                        <option value="8-10">इयत्ता 8 ते 10</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">एकूण विद्यार्थी</label>
                    <input type="number" name="total_students" id="edit_total" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">महिना</label>
                        <select name="month" id="edit_month" class="form-select" required>
                            <?php for ($m = 1; $m <= 12; $m++) : ?>
                                <option value="<?= $m ?>"><?= date("F", mktime(0, 0, 0, $m, 10)) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">वर्ष</label>
                        <input type="number" name="year" id="edit_year" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary w-100">अपडेट करा</button>
            </div>
        </form>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $('.edit-btn').on('click', function() {
            const id = $(this).data('id');

            $.ajax({
                url: '<?= base_url('StudentStrength/edit/') ?>/' + id,
                method: 'GET',
                success: function(data) {
                    // Set form action dynamically
                    $('#editForm').attr('action', '<?= base_url('StudentStrength/update/') ?>/' + id);

                    // Fill modal fields
                    $('#edit_category').val(data.category);
                    $('#edit_total').val(data.total_students);
                    $('#edit_month').val(data.month);
                    $('#edit_year').val(data.year);

                    // Show modal
                    $('#editModal').modal('show');
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>