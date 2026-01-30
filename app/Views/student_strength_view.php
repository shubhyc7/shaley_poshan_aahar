<?= $this->extend('main') ?>
<?= $this->section('content') ?>
<title>शालेय पोषण आहार प्रणाली | विद्यार्थी संख्या यादी</title>

<style>
/* Student Strength - Mobile Responsive */
.student-strength-card .card-header.d-flex { flex-wrap: wrap; gap: 0.5rem; }
.student-strength-card .card-header .btn { min-height: 44px; }
@media (max-width: 768px) {
    .student-strength-card .card-header.d-flex { flex-direction: column; align-items: stretch; }
    .student-strength-card .card-header .btn { width: 100%; }
    .student-strength-card .filter-form .col-md-2, .student-strength-card .filter-form .col-md-3 { max-width: 100%; }
}
@media (max-width: 576px) {
    .student-strength-card .table th, .student-strength-card .table td { padding: 0.5rem; font-size: 0.875rem; }
    .student-strength-card .btn-sm { min-width: 44px; min-height: 44px; padding: 0.5rem; }
    .student-strength-card .table-responsive { margin: 0 -0.75rem; overflow-x: auto; -webkit-overflow-scrolling: touch; }
}
</style>

<div class="card shadow-sm border-0 mb-4 student-strength-card">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap">
        <h5 class="mb-0 text-primary fw-bold">विद्यार्थी संख्या यादी</h5>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= base_url('StudentStrength/export?category=' . urlencode($filterCategory ?? '')) ?>" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel me-1"></i> एक्सेलमध्ये निर्यात करा
            </a>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus me-1"></i> नवीन संख्या जोडा
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('status')) : ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= esc(session()->getFlashdata('status')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="GET" action="<?= base_url('StudentStrength') ?>" class="row g-3 align-items-end filter-form">
            <div class="col-12 col-md-4">
                <label class="form-label fw-bold">इयत्ता निवडा</label>
                <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">सर्व</option>
                    <option value="1-5" <?= ($filterCategory ?? '') == '1-5' ? 'selected' : '' ?>>1-5</option>
                    <option value="6-8" <?= ($filterCategory ?? '') == '6-8' ? 'selected' : '' ?>>6-8</option>
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
                            <td><?= esc($row['id']) ?></td>
                            <td>
                                <span class="badge <?= ($row['category'] ?? '') == '6-8' ? 'bg-info' : 'bg-primary' ?>">
                                    इयत्ता <?= esc($row['category'] ?? '') ?>
                                </span>
                            </td>
                            <td><strong><?= esc($row['total_students']) ?></strong></td>
                            <td class="btn-action-group">
                                <button type="button" class="btn btn-sm btn-outline-primary edit-btn" data-id="<?= esc($row['id']) ?>" title="संपादित करा"><i class="fas fa-edit"></i></button>
                                <a href="<?= base_url('StudentStrength/delete/' . $row['id'] . '?category=' . urlencode($filterCategory ?? '')) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('हटवायचे?')" title="हटवा"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form action="<?= base_url('StudentStrength/store') ?>" method="POST" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addModalLabel">विद्यार्थी संख्या जोडा</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                    <input type="number" name="total_students" class="form-control" placeholder="विद्यार्थ्यांची संख्या प्रविष्ट करा" min="1" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">रद्द करा</button>
                <button type="submit" class="btn btn-primary">जतन करा</button>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form id="editForm" method="POST" class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="editModalLabel">विद्यार्थी संख्या संपादित करा</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">इयत्ता</label>
                    <select name="category" id="edit_category" class="form-select" required>
                        <option value="1-5">1-5</option>
                        <option value="6-8">6-8</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">एकूण विद्यार्थी</label>
                    <input type="number" name="total_students" id="edit_total" class="form-control" min="1" required>
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
                    $('#editForm').attr('action', '<?= base_url('StudentStrength/update/') ?>/' + id);
                    $('#edit_category').val(data.category);
                    $('#edit_total').val(data.total_students);
                    $('#editModal').modal('show');
                },
                error: function() {
                    alert('डेटा लोड करताना त्रुटी आली.');
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>