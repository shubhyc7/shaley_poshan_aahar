<?= $this->extend('main') ?>
<?= $this->section('content') ?>
<title>शालेय पोषण आहार प्रणाली | विद्यार्थी संख्या यादी</title>

<div class="card shadow-sm border-0 mb-4">


    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">

        <h5 class="mb-0 text-primary fw-bold">विद्यार्थी संख्या यादी</h5>
        <div>
            <a href="<?= base_url('StudentStrength/export?category=' . $filterCategory) ?>" class="btn btn-success me-2">
                <i class="fas fa-file-excel"></i> एक्सेलमध्ये निर्यात करा
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i> नवीन संख्या जोडा
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

        <form method="GET" action="<?= base_url('StudentStrength') ?>" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold">इयत्ता निवडा</label>
                <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">सर्व</option>
                    <option value="1-5" <?= $filterCategory == '1-5' ? 'selected' : '' ?>>1-5</option>
                    <option value="6-8" <?= $filterCategory == '6-8' ? 'selected' : '' ?>>6-8</option>
                </select>
            </div>
        </form>



        <div class="table-responsive bg-white rounded shadow-sm border mt-4">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>क्रमांक</th>
                        <th>इयत्ता</th>
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
                            <td><strong><?= $row['total_students'] ?></strong></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary edit-btn" data-id="<?= $row['id'] ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="<?= base_url('StudentStrength/delete/' . $row['id'] . '?category=' . $filterCategory) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('हटवायचे?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('StudentStrength/store') ?>" method="POST" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">विद्यार्थी संख्या जोडा</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">इयत्ता निवडा</label>
                    <select name="category" class="form-select" required>
                        <option value="1-5">1-5</option>
                        <option value="6-8">6-8</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">एकूण विद्यार्थी</label>
                    <input type="number" name="total_students" class="form-control" placeholder="विद्यार्थ्यांची संख्या प्रविष्ट करा" required>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">रद्द करा</button>
                <button type="submit" class="btn btn-primary px-4">जतन करा</button>
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
                    <label class="fw-bold">इयत्ता</label>
                    <select name="category" id="edit_category" class="form-select" required>
                        <option value="1-5">1-5</option>
                        <option value="6-8">6-8</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">एकूण विद्यार्थी</label>
                    <input type="number" name="total_students" id="edit_total" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">रद्द करा</button>
                <button type="submit" class="btn btn-warning">अपडेट करा</button>
            </div>
        </form>
    </div>
</div>

<script src="<?php echo base_url('js/jquery-3.6.0.min.js'); ?>"></script>

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
                    // Show modal
                    $('#editModal').modal('show');
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>