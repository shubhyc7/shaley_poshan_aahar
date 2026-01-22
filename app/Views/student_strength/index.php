<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Student Strength Master</h2>
    <div>
        <a href="<?= base_url('StudentStrength/export') ?>" class="btn btn-success me-2">
            <i class="fas fa-file-excel"></i> Export to Excel
        </a>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus"></i> Add New Strength
        </button>
    </div>
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
                    <th>ID</th>
                    <th>Category</th>
                    <th>Month / Year</th>
                    <th>Total Students</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $row) : ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td>
                            <span class="badge <?= $row['category'] == '5-8' ? 'bg-info' : 'bg-primary' ?>">
                                Class <?= $row['category'] ?>
                            </span>
                        </td>
                        <td><?= date("F", mktime(0, 0, 0, $row['month'], 10)) ?> - <?= $row['year'] ?></td>
                        <td><strong><?= $row['total_students'] ?></strong></td>
                        <td>
                            <button type="button" class="btn btn-primary btn-sm edit-btn" data-id="<?= $row['id'] ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="<?= base_url('StudentStrength/delete/' . $row['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">
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
            <div class="modal-header">
                <h5 class="modal-title">Add Student Strength</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Select Category</label>
                    <select name="category" class="form-select" required>
                        <option value="5-8">Class 5 to 8</option>
                        <option value="8-10">Class 8 to 10</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Total Students</label>
                    <input type="number" name="total_students" class="form-control" placeholder="Enter number of students" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Month</label>
                        <select name="month" class="form-select" required>
                            <?php for ($m = 1; $m <= 12; $m++) : ?>
                                <option value="<?= $m ?>" <?= $m == date('n') ? 'selected' : '' ?>>
                                    <?= date("F", mktime(0, 0, 0, $m, 10)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Year</label>
                        <input type="number" name="year" class="form-control" value="<?= date('Y') ?>" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success px-4">Save Strength</button>
            </div>
        </form>
    </div>
</div>



<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editForm" method="POST" class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Edit Student Strength</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="fw-bold">Category</label>
                    <select name="category" id="edit_category" class="form-select" required>
                        <option value="5-8">Class 5 to 8</option>
                        <option value="8-10">Class 8 to 10</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Total Students</label>
                    <input type="number" name="total_students" id="edit_total" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Month</label>
                        <select name="month" id="edit_month" class="form-select" required>
                            <?php for ($m = 1; $m <= 12; $m++) : ?>
                                <option value="<?= $m ?>"><?= date("F", mktime(0, 0, 0, $m, 10)) ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Year</label>
                        <input type="number" name="year" id="edit_year" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary w-100">Update Strength</button>
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