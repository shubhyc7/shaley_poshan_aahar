<?= $this->extend('main') ?>
<?= $this->section('content') ?>
<title>शालेय पोषण आहार प्रणाली | वापर दर</title>

<style>
/* Item Rates - Mobile Responsive */
.item-rates-card .card-header.d-flex { flex-wrap: wrap; gap: 0.5rem; }
.item-rates-card .card-header .btn { min-height: 44px; }
@media (max-width: 768px) {
    .item-rates-card .card-header.d-flex { flex-direction: column; align-items: stretch; }
    .item-rates-card .card-header .btn { width: 100%; }
    .item-rates-card .filter-form .col-md-4 { max-width: 100%; }
}
@media (max-width: 576px) {
    .item-rates-card .table th, .item-rates-card .table td { padding: 0.5rem; font-size: 0.875rem; }
    .item-rates-card .btn-sm { min-width: 44px; min-height: 44px; padding: 0.5rem; }
    .item-rates-card .table-responsive { margin: 0 -0.75rem; overflow-x: auto; -webkit-overflow-scrolling: touch; }
}
</style>

<div class="card shadow-sm border-0 mb-4 item-rates-card">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap">
        <h5 class="mb-0 text-primary fw-bold">वापर दर (प्रति विद्यार्थी)</h5>
        <div class="btn-header-group">
            <a href="<?= base_url('ItemRates/export?category=' . urlencode($filterCategory ?? '') . (!empty($filterItemType) ? '&item_type=' . urlencode($filterItemType) : '')) ?>" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel me-1"></i> एक्सेलमध्ये निर्यात करा
            </a>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#rateModal">
                <i class="fas fa-plus me-1"></i> नवीन दर सेट करा
            </button>
        </div>
    </div>

    <div class="card-body">
        <?php if (session()->getFlashdata('status')) : ?>
            <div class="alert-wrapper">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?= esc(session()->getFlashdata('status')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert-wrapper">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?= esc(session()->getFlashdata('error')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>

        <form method="GET" action="<?= base_url('ItemRates') ?>" class="row g-3 align-items-end filter-form">
            <div class="col-12 col-md-3">
                <label class="form-label fw-bold">इयत्ता निवडा</label>
                <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">सर्व</option>
                    <option value="1-5" <?= ($filterCategory ?? '') == '1-5' ? 'selected' : '' ?>>1-5</option>
                    <option value="6-8" <?= ($filterCategory ?? '') == '6-8' ? 'selected' : '' ?>>6-8</option>
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-bold">वस्तूचा प्रकार निवडा</label>
                <select name="item_type" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">सर्व प्रकार</option>
                    <option value="MAIN" <?= ($filterItemType ?? '') == 'MAIN' ? 'selected' : '' ?>>मुख्य (प्राथमिक धान्य)</option>
                    <option value="SUPPORT" <?= ($filterItemType ?? '') == 'SUPPORT' ? 'selected' : '' ?>>सहाय्यक (मसाले/तेल/मीठ)</option>
                </select>
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
                        <th>प्रति विद्यार्थी प्रमाण</th>
                        <th>एकक</th>
                        <th>क्रिया</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rates as $rate) : ?>
                        <tr>
                            <td><strong><?= esc($rate['id']) ?></strong></td>
                            <td><strong><?= esc($rate['item_name']) ?></strong></td>
                            <td>
                                <span class="badge <?= ($rate['item_type'] ?? '') == 'MAIN' ? 'bg-info' : 'bg-secondary' ?>">
                                    <?= ($rate['item_type'] ?? '') == 'MAIN' ? 'मुख्य' : 'सहाय्यक'; ?>
                                </span>
                            </td>
                            <td><span class="badge <?= ($rate['category'] ?? '') == '6-8' ? 'bg-info' : 'bg-primary' ?>">इयत्ता <?= esc($rate['category'] ?? '') ?></span></td>
                            <td><?= esc($rate['per_student_qty']) ?></td>
                            <td><strong><?= esc($rate['unit'] ?? '') ?></strong></td>
                            <td class="btn-action-group">
                                <button type="button" class="btn btn-sm btn-outline-primary edit-btn" data-id="<?= esc($rate['id']) ?>" title="संपादित करा"><i class="fas fa-edit"></i></button>
                                <a href="<?= base_url('ItemRates/delete/' . $rate['id'] . '?category=' . urlencode($filterCategory ?? '') . (!empty($filterItemType) ? '&item_type=' . urlencode($filterItemType) : '')) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('हटवायचे?')" title="हटवा"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>




<div class="modal fade" id="rateModal" tabindex="-1" aria-labelledby="rateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form action="<?= base_url('ItemRates/store') ?>" method="POST" class="modal-content">
            <input type="hidden" name="filter_item_type" value="<?= esc($filterItemType ?? '') ?>">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="rateModalLabel">वापर दर सेट करा</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">इयत्ता निवडा</label>
                    <select name="category" class="form-select" required>
                        <option value="1-5" <?= old('category') == '1-5' ? 'selected' : '' ?>>1-5</option>
                        <option value="6-8" <?= old('category') == '6-8' ? 'selected' : '' ?>>6-8</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">वस्तू निवडा</label>
                    <select name="item_id" class="form-select" required>
                        <?php foreach ($items as $item) : ?>
                            <option value="<?= $item['id'] ?>" <?= old('item_id') == $item['id'] ? 'selected' : '' ?>><?= esc($item['item_name']) ?> (<?= esc($item['unit']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">प्रति विद्यार्थी प्रमाण</label>
                    <input type="number" step="0.0001" min="0.0001" name="per_student_qty" class="form-control" value="<?= esc(old('per_student_qty')) ?>" required placeholder="0.0000">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">रद्द करा</button>
                <button type="submit" class="btn btn-primary">जतन करा</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editRateModal" tabindex="-1" aria-labelledby="editRateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form id="editRateForm" method="POST" class="modal-content">
            <input type="hidden" name="filter_item_type" value="<?= esc($filterItemType ?? '') ?>">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="editRateModalLabel">वापर दर संपादित करा</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                    <label class="form-label fw-bold">वस्तू</label>
                    <select name="item_id" id="edit_item_id" class="form-select" required>
                        <?php foreach ($items as $item) : ?>
                            <option value="<?= $item['id'] ?>"><?= esc($item['item_name']) ?> (<?= esc($item['unit']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">प्रति विद्यार्थी प्रमाण</label>
                    <input type="number" step="0.0001" min="0.0001" name="per_student_qty" id="edit_qty" class="form-control" required placeholder="0.0000">
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
                url: '<?= base_url('ItemRates/edit/') ?>/' + id,
                method: 'GET'
            })
            .done(function(data) {
                if (data.error) {
                    alert('डेटा सापडला नाही.');
                    return;
                }
                $('#editRateForm').attr('action', '<?= base_url('ItemRates/update/') ?>/' + id);
                $('#edit_category').val(data.category);
                $('#edit_item_id').val(data.item_id);
                $('#edit_qty').val(data.per_student_qty);
                $('#editRateModal').modal('show');
            })
            .fail(function() {
                alert('डेटा लोड करताना त्रुटी आली.');
            });
        });

    });
</script>

<?= $this->endSection() ?>