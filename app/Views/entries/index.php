<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-primary fw-bold"><i class="fas fa-plus-circle me-2"></i>दैनंदिन बहु-वस्तू नोंद</h5>
        <a href="<?= base_url('Entries/export') ?>" class="btn btn-success btn-sm">
            <i class="fas fa-file-excel"></i> एक्सेलमध्ये निर्यात करा
        </a>
    </div>

    <?php if (session()->getFlashdata('status')) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('status') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card-body p-0">
        <form action="<?= base_url('entries/store') ?>" method="POST" id="entryForm">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="min-width: 140px;">तारीख</th>
                            <th style="min-width: 140px;">श्रेणी</th>
                            <th style="width: 100px;">एकूण</th>
                            <th style="width: 100px;">उपस्थित</th>
                            <?php foreach ($main_items as $mi) : ?>
                                <th class="text-center bg-primary small"><?= $mi['item_name'] ?></th>
                            <?php endforeach; ?>
                            <?php foreach ($support_items as $si) : ?>
                                <th class="text-center bg-secondary small"><?= $si['item_name'] ?></th>
                            <?php endforeach; ?>
                            <th class="text-center">क्रिया</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-info">
                            <td>
                                <input type="date" id="entry_date" name="entry_date" class="form-control form-control-sm mb-1" value="<?= date('Y-m-d') ?>">

                            </td>

                            <td>
                                <select id="category" name="category" class="form-select form-select-sm">
                                    <option value="5-8">5-8</option>
                                    <option value="8-10">8-10</option>
                                </select>
                                <input type="hidden" name="student_strength" id="student_strength_val">
                            </td>
                            <td>
                                <small class="text-muted" style="font-size: 0.7rem;"><span id="max_strength">0</span></small>
                            </td>
                            <td>
                                <input type="number" id="present_students" name="present_students" class="form-control form-control-sm" placeholder="Qty">
                            </td>

                            <?php foreach ($main_items as $mi) : ?>
                                <td class="text-center main-item-cell">
                                    <input type="checkbox" name="main_item_id[]" value="<?= $mi['id'] ?>" class="item-chk main-item-chk mb-1">
                                    <input type="hidden" name="main_item_qty[]" id="qty_<?= $mi['id'] ?>" value="0">
                                    <div class="small fw-bold text-primary display-qty" id="display_<?= $mi['id'] ?>" style="font-size: 0.75rem;">0.000</div>
                                </td>
                            <?php endforeach; ?>

                            <?php foreach ($support_items as $si) : ?>
                                <td class="text-center">
                                    <input type="hidden" name="support_item_id[]" value="<?= $si['id'] ?>">
                                    <input type="hidden" name="support_qty[]" id="qty_<?= $si['id'] ?>" value="0">
                                    <div class="small fw-bold text-secondary display-qty" id="display_<?= $si['id'] ?>" style="font-size: 0.75rem;">0.000</div>
                                </td>
                            <?php endforeach; ?>

                            <td class="text-center">
                                <button type="submit" class="btn btn-success btn-sm px-3">जतन करा</button>
                            </td>
                        </tr>

                        <?php if (empty($entries)) : ?>
                            <tr>
                                <td colspan="<?= (count($main_items) + count($support_items) + 3) ?>" class="text-center py-3 text-muted">या महिन्यासाठी कोणतीही नोंद आढळली नाही.</td>
                            </tr>
                        <?php else : ?>
                            <?php
                            $db = \Config\Database::connect();
                            foreach ($entries as $row) : ?>
                                <tr class="bg-white">
                                    <td class="small">
                                        <div class="fw-bold"><?= date('d-M-Y', strtotime($row['entry_date'])) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border" style="font-size: 0.65rem;">इयत्ता <?= $row['category'] ?></span>
                                    </td>
                                    <td class="text-center fw-bold text-muted small"><?= $row['total_students'] ?></td>
                                    <td class="text-center fw-bold text-muted small"><?= $row['present_students'] ?></td>

                                    <?php foreach ($main_items as $mi) : ?>
                                        <td class="text-center small">
                                            <?php if ($row['item_id'] == $mi['id']) : ?>
                                                <span class="text-primary fw-bold"><?= number_format($row['qty'], 3) ?></span>
                                            <?php else : ?>
                                                <span class="text-light">0.000</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>

                                    <?php foreach ($support_items as $si) : ?>
                                        <td class="text-center small">
                                            <?php
                                            $sv = $db->table('daily_aahar_entries_support_items')
                                                ->where(['main_entry_id' => $row['id'], 'support_item_id' => $si['id'], 'is_disable' => '0'])
                                                ->get()->getRow();
                                            echo $sv ? '<span class="text-secondary fw-bold">' . number_format($sv->qty, 3) . '</span>' : '<span class="text-light">0.000</span>';
                                            ?>
                                        </td>
                                    <?php endforeach; ?>

                                    <td class="text-center">
                                        <a href="<?= base_url('entries/delete/' . $row['id']) ?>" class="text-danger" onclick="return confirm('ही नोंद हटवायची?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        fetchStrength();

        $('#entry_date, #category').on('change', fetchStrength);
        $('#present_students, .main-item-chk').on('input change', runCalculation);

        function fetchStrength() {
            const date = $('#entry_date').val();
            const cat = $('#category').val();
            if (date && cat) {
                $.ajax({
                    url: '<?= base_url('entries/getStrengthAjax') ?>',
                    method: 'POST',
                    data: {
                        date,
                        category: cat
                    },
                    success: function(res) {
                        $('#max_strength').text(res.total);
                        $('#student_strength_val').val(res.total);
                        runCalculation();
                    }
                });
            }
        }

        function runCalculation() {
            const date = $('#entry_date').val();
            const cat = $('#category').val();
            const present = $('#present_students').val();

            if (date && cat && present > 0) {
                $.ajax({
                    url: '<?= base_url('entries/calculate') ?>',
                    method: 'POST',
                    data: {
                        date,
                        category: cat,
                        present
                    },
                    success: function(res) {
                        const rates = res.all_rates || res.rates;

                        // Calculate Main Items
                        $('.main-item-chk').each(function() {
                            const id = $(this).val();
                            if ($(this).is(':checked')) {
                                const val = rates[id] || "0.000";
                                $(`#display_${id}`).text(val);
                                $(`#qty_${id}`).val(val);
                            } else {
                                $(`#display_${id}`).text('0.000');
                                $(`#qty_${id}`).val(0);
                            }
                        });

                        // Calculate Support Items
                        <?php foreach ($support_items as $si) : ?>
                            var sVal = (rates && rates[<?= $si['id'] ?>]) ? rates[<?= $si['id'] ?>] : "0.000";
                            $('#display_<?= $si['id'] ?>').text(sVal);
                            $('#qty_<?= $si['id'] ?>').val(sVal);
                        <?php endforeach; ?>
                    }
                });
            } else {
                $('.display-qty').text('0.000');
                $('input[type="hidden"][id^="qty_"]').val(0);
            }
        }

        $('#entryForm').on('submit', function(e) {
            if ($('.main-item-chk:checked').length === 0) {
                e.preventDefault();
                alert("कृपया किमान एक मुख्य वस्तू (तांदूळ, डाळ, इ.) निवडा");
            }
        });
    });
</script>

<?= $this->endSection() ?>