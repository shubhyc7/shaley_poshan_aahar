<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">New Daily Consumption Entry</h4>
    </div>
    <div class="card-body">
        <form action="<?= base_url('entries/store') ?>" method="POST">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="entry_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Total Enrolled</label>
                    <input type="number" name="total_students" class="form-control bg-light" value="<?= $total_enrolled ?>" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Present Students</label>
                    <input type="number" id="present_students" name="present_students" class="form-control" placeholder="0" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Select Main Item</label>
                    <select name="item_id" id="item_id" class="form-select" required>
                        <option value="">-- Select Rice/Dal --</option>
                        <?php foreach ($main_items as $item) : ?>
                            <option value="<?= $item['id'] ?>"><?= $item['item_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <hr class="my-4">

            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5>Calculated Consumption</h5>
                    <p class="text-muted">Qty = Present Students × Rate per Student</p>
                </div>
                <div class="col-md-6 text-end">
                    <div class="display-6 text-primary">
                        <span id="display_qty">0.000</span> <small class="fs-6">kg/ltr</small>
                    </div>
                    <input type="hidden" name="calculated_qty" id="calculated_qty" value="0">
                </div>
            </div>

            <div class="mt-4 card border-secondary">
                <div class="card-header bg-secondary text-white">
                    <i class="fas fa-bottles me-2"></i> Support Items (Oil, Salt, Spices)
                </div>
                <div class="card-body">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th style="width: 200px;">Consumption Qty</th>
                            </tr>
                        </thead>
                        <tbody id="support-items-body">
                            <?php
                            // Fetch items where item_type = 'SUPPORT'
                            $db = \Config\Database::connect();
                            $supports = $db->table('items')->where('item_type', 'SUPPORT')->get()->getResultArray();

                            foreach ($supports as $s) : ?>
                                <tr>
                                    <td><?= $s['item_name'] ?> (<?= $s['unit'] ?>)</td>
                                    <td>
                                        <input type="hidden" name="support_item_id[]" value="<?= $s['id'] ?>">
                                        <div class="input-group">
                                            <input type="number" step="0.001" name="support_qty[]" class="form-control support-calc" data-itemid="<?= $s['id'] ?>" value="0">
                                            <span class="input-group-text"><?= $s['unit'] ?></span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-success btn-lg px-5">Save Daily Entry</button>
                <a href="<?= base_url('entries') ?>" class="btn btn-light btn-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    const rates = <?= $rates ?>; // Passed from Controller

    // Inside your existing calculate() function in create.php
    function calculate() {
        const present = parseFloat($('#present_students').val()) || 0;

        // ... (Your existing Main Item calculation) ...

        // Calculate Support Items
        $('.support-calc').each(function() {
            const s_itemId = $(this).data('itemid');
            const s_rate = rates.find(r => r.item_id == s_itemId);

            if (s_rate && present > 0) {
                const s_total = present * parseFloat(s_rate.per_student_qty);
                $(this).val(s_total.toFixed(3));
            } else {
                $(this).val(0);
            }
        });
    }

    $('#present_students, #item_id').on('input change', calculate);
</script>

<?= $this->endSection() ?>