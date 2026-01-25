<?= $this->extend('main') ?>
<?= $this->section('content') ?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-primary fw-bold">स्टॉक नोंद</h5>
        <div>
            <a href="<?= base_url("Stock/export?month=$month&year=$year&item_id=$selected_item") ?>" class="btn btn-success me-2">
                <i class="fas fa-file-excel"></i> एक्सेलमध्ये निर्यात करा
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStockModal">
                <i class="fas fa-plus"></i> नवीन स्टॉक नोंद
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
        <form method="GET" action="<?= base_url('Stock') ?>" id="filterForm" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small fw-bold">महिना निवडा</label>
                <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                    <?php for ($m = 1; $m <= 12; $m++) : ?>
                        <option value="<?= $m ?>" <?= $m == $month ? 'selected' : '' ?>><?= date("F", mktime(0, 0, 0, $m, 10)) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold">वर्ष निवडा</label>
                <input type="number" name="year" class="form-control form-control-sm" value="<?= $year ?>" onchange="this.form.submit()">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">वस्तू निवडा</label>
                <select name="item_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">सर्व वस्तू</option>
                    <?php foreach ($items as $it) : ?>
                        <option value="<?= $it['id'] ?>" <?= $selected_item == $it['id'] ? 'selected' : '' ?>><?= $it['item_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

        </form>

        <div class="table-responsive bg-white rounded shadow-sm border mt-4">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light text-center small">
                    <tr class="table-dark">
                        <th>तारीख</th>
                        <th>वस्तू (एकक)</th>
                        <th>प्रकार</th>
                        <th>प्रारंभिक (Opening)</th>
                        <th>आवक / खर्च (Qty)</th>
                        <th>शिल्लक (Closing)</th>
                        <th>क्रिया</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $tIn = 0;
                    $tOut = 0;
                    foreach ($transactions as $row) :
                        if ($row['transaction_type'] == 'OUT') $tOut += $row['quantity'];
                        else $tIn += $row['quantity'];
                    ?>
                        <tr class="small">
                            <td class="text-center"><?= date('d-m-Y', strtotime($row['transaction_date'])) ?></td>
                            <td><strong><?= $row['item_name'] ?></strong> <small class="text-muted">(<?= $row['unit'] ?>)</small></td>
                            <td class="text-center">
                                <span class="badge <?= $row['transaction_type'] == 'OUT' ? 'bg-danger' : ($row['transaction_type'] == 'OPENING' ? 'bg-info' : 'bg-success') ?>">
                                    <?= $row['transaction_type'] ?>
                                </span>
                            </td>
                            <td class="text-end text-muted"><?= number_format($row['opening_bal'], 3) ?></td>
                            <td class="text-end fw-bold <?= $row['transaction_type'] == 'OUT' ? 'text-danger' : 'text-success' ?>">
                                <?= $row['transaction_type'] == 'OUT' ? '-' : '+' ?> <?= number_format($row['quantity'], 3) ?>
                            </td>
                            <td class="text-end fw-bold text-primary"><?= number_format($row['closing_bal'], 3) ?></td>
                            <td class="text-center">
                                <?php if ($row['transaction_type'] != 'OUT') : ?>
                                    <button class="btn btn-sm btn-link edit-stock-btn" data-id="<?= $row['id'] ?>"><i class="fas fa-edit"></i></button>
                                    <a href="<?= base_url("Stock/delete/{$row['id']}?month=$month&year=$year&item_id=$selected_item") ?>" class="text-danger p-1" onclick="return confirm('हटवायचे?')"><i class="fas fa-trash"></i></a>
                                <?php else : ?>
                                    <i class="fas fa-lock text-muted" title="Entry Screen Linked"></i>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-dark">
                    <?php
                    // 1. Calculate Total Opening Balance for filtered context
                    $totalOpening = 0;
                    if ($selected_item) {
                        // If one item is selected, use its specific opening balance
                        $totalOpening = $opening_balances[$selected_item] ?? 0;
                    } else {
                        // If all items are shown, sum up all their opening balances for the month
                        $totalOpening = array_sum($opening_balances);
                    }

                    // 2. Transaction Totals (already calculated in your loop)
                    // Note: Ensure $tIn and $tOut are initialized before your foreach loop

                    // 3. Final Closing
                    $finalClosing = ($totalOpening + $tIn) - $tOut;
                    ?>
                    <tr class="align-middle">
                        <td colspan="3" class="text-end fw-bold">एकूण सारांश (Total Summary):</td>
                        <td class="text-end">
                            <span class="d-block small text-muted">Total Opening</span>
                            <strong><?= number_format($totalOpening, 3) ?></strong>
                        </td>
                        <td class="text-center">
                            <div class="d-inline-block text-start px-2">
                                <span class="d-block small text-success font-weight-bold">Total IN: + <?= number_format($tIn, 3) ?></span>
                                <span class="d-block small text-danger font-weight-bold">Total OUT: - <?= number_format($tOut, 3) ?></span>
                            </div>
                        </td>
                        <td class="text-end">
                            <span class="d-block small text-info">Final Closing</span>
                            <strong class="text-warning"><?= number_format($finalClosing, 3) ?></strong>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>


<div class="modal fade" id="addStockModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('Stock/store') ?>" method="POST" id="editStockForm" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5>स्टॉक नोंदवा</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="edit_id">

                <input type="hidden" name="filter_month" value="<?= $month ?>">
                <input type="hidden" name="filter_year" value="<?= $year ?>">
                <input type="hidden" name="filter_item_id" value="<?= $selected_item ?>">

                <div class="mb-3">
                    <label class="small fw-bold">प्रकार</label>
                    <select name="transaction_type" id="edit_type" class="form-select" required>
                        <option value="IN">IN (आवक)</option>
                        <option value="OPENING">OPENING (सुरुवातीचा साठा)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold">तारीख</label>
                    <input type="date" name="transaction_date" id="edit_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold">वस्तू</label>
                    <select name="item_id" id="edit_item_id" class="form-select" required>
                        <?php foreach ($items as $it) : ?>
                            <option value="<?= $it['id'] ?>"><?= $it['item_name'] ?> (<?= $it['unit'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold">परिमाण (Qty)</label>
                    <input type="number" step="0.001" name="quantity" id="edit_qty" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold">शेरा</label>
                    <textarea name="remarks" id="edit_remarks" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer"><button type="submit" class="btn btn-primary w-100">जतन करा</button></div>
        </form>
    </div>
</div>

<script src="<?php echo base_url('js/jquery-3.6.0.min.js'); ?>"></script>

<script>
    $('.edit-stock-btn').on('click', function() {
        const id = $(this).data('id');
        $.get('<?= base_url('Stock/edit/') ?>/' + id, function(data) {
            $('#edit_id').val(data.id);
            $('#edit_type').val(data.transaction_type);
            $('#edit_date').val(data.transaction_date);
            $('#edit_item_id').val(data.item_id);
            $('#edit_qty').val(data.quantity);
            $('#edit_remarks').val(data.remarks);
            $('#addStockModal').modal('show');
        });
    });
</script>
<?= $this->endSection() ?>