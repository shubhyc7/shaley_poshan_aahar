<?= $this->extend('main') ?>
<?= $this->section('content') ?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-primary fw-bold">दैनंदिन बहु-वस्तू नोंद</h5>
    </div>
    <div class="card shadow-sm border-0 mb-4">
        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header bg-white py-3">
                <form method="GET" action="<?= base_url('Entries') ?>" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">महिना निवडा</label>
                        <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                            <?php for ($m = 1; $m <= 12; $m++) : ?>
                                <option value="<?= $m ?>" <?= $m == $filterMonth ? 'selected' : '' ?>>
                                    <?= date("F", mktime(0, 0, 0, $m, 10)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">वर्ष निवडा</label>
                        <input type="number" name="year" class="form-control form-control-sm" value="<?= $filterYear ?>" onchange="this.form.submit()">
                    </div>
                    <!-- <div class="col-md-2">
                        <button type="submit" class="btn btn-dark btn-sm w-100">फिल्टर</button>
                    </div> -->
                    <div class="col-md-7 text-end">
                        <a href="<?= base_url('Entries/export?month=' . $filterMonth . '&year=' . $filterYear) ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> एक्सेलमध्ये निर्यात करा
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body p-0">

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
            <form action="<?= base_url('entries/store') ?>" method="POST" id="entryForm">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-dark text-center">
                            <tr>
                                <th style="width: 50px;">अ.क्र.</th>
                                <th style="min-width: 10px;">तारीख</th>
                                <th style="width: 150px;">इयत्ता</th>
                                <th style="width: 70px;">एकूण</th>
                                <th style="width: 70px;">उपस्थित</th>
                                <?php foreach ($main_items as $mi) : ?>
                                    <th class="bg-primary small"><?= $mi['item_name'] ?></th>
                                <?php endforeach; ?>
                                <?php foreach ($support_items as $si) : ?>
                                    <th class="bg-secondary small"><?= $si['item_name'] ?></th>
                                <?php endforeach; ?>
                                <th>क्रिया</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table-warning border-primary" style="font-size: 0.8rem;">
                                <td colspan="5" class="text-end fw-bold text-dark">
                                    शिल्लक साठा (Month Stock):<br>
                                    <small class="text-muted">(Opening + Received)</small>
                                </td>
                                <?php foreach (array_merge($main_items, $support_items) as $item) :
                                    $stock = $monthly_stock_logic[$item['id']];
                                ?>
                                    <td class="text-center p-1">
                                        <div class="fw-bold"><?= number_format($stock['available'], 3) ?></div>
                                        <a href="<?= base_url('Stock?item_id=' . $item['id'] . '&add_mode=1') ?>" class="btn btn-xs btn-outline-dark py-0" style="font-size: 0.6rem;">
                                            + Stock
                                        </a>
                                    </td>
                                <?php endforeach; ?>
                                <td></td>
                            </tr>

                            <tr class="table-info">
                                <td>#</td>
                                <td><input type="date" name="entry_date" id="entry_date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>"></td>
                                <td>
                                    <select name="category" id="category" class="form-select form-select-sm">
                                        <option value="6-8">6-8</option>
                                        <option value="9-10">9-10</option>
                                    </select>
                                    <input type="hidden" name="student_strength" id="student_strength_val">
                                </td>
                                <td><small id="max_strength">0</small></td>
                                <td><input type="number" name="present_students" id="present_students" class="form-control form-control-sm"></td>

                                <?php foreach ($main_items as $mi) : ?>
                                    <td class="text-center">
                                        <input type="checkbox" name="main_item_id[]" value="<?= $mi['id'] ?>" class="main-item-chk">
                                        <input type="hidden" name="main_item_qty[]" id="qty_<?= $mi['id'] ?>" value="0">
                                        <div class="small fw-bold text-primary display-qty" id="display_<?= $mi['id'] ?>">0.000</div>
                                    </td>
                                <?php endforeach; ?>

                                <?php foreach ($support_items as $si) : ?>
                                    <td class="text-center">
                                        <input type="hidden" name="support_item_id[]" value="<?= $si['id'] ?>">
                                        <input type="hidden" name="support_qty[]" id="qty_<?= $si['id'] ?>" value="0">
                                        <div class="small fw-bold text-secondary display-qty" id="display_<?= $si['id'] ?>">0.000</div>
                                    </td>
                                <?php endforeach; ?>
                                <td class="text-center"><button type="submit" class="btn btn-success btn-sm w-100">Save</button></td>
                            </tr>

                            <?php
                            $db = \Config\Database::connect();
                            $sr = 1;
                            $sumTotal = 0;
                            $sumPresent = 0;
                            $itemTotals = [];
                            foreach ($entries as $row) :
                                $sumTotal += $row['total_students'];
                                $sumPresent += $row['present_students'];
                                $childItems = $db->table('daily_aahar_entries_items')->where(['daily_aahar_entries_id' => $row['id'], 'is_disable' => 0])->get()->getResultArray();
                                $qtyMap = array_column($childItems, 'qty', 'item_id');
                            ?>
                                <tr class="bg-white">
                                    <td class="text-center small"><?= $sr++ ?></td>
                                    <td class="small"><?= date('d-m-Y', strtotime($row['entry_date'])) ?></td>
                                    <td class="text-center small"><?= $row['category'] ?></td>
                                    <td class="text-center small"><?= $row['total_students'] ?></td>
                                    <td class="text-center small text-success fw-bold"><?= $row['present_students'] ?></td>
                                    <?php foreach (array_merge($main_items, $support_items) as $it) :
                                        $q = $qtyMap[$it['id']] ?? 0;
                                        $itemTotals[$it['id']] = ($itemTotals[$it['id']] ?? 0) + $q;
                                    ?>
                                        <td class="text-center small"><?= $q > 0 ? number_format($q, 3) : '-' ?></td>
                                    <?php endforeach; ?>
                                    <td class="text-center">
                                        <a href="<?= base_url('entries/delete/' . $row['id']) ?>" class="text-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>

                        <tfoot class="table-dark">
                            <tr>
                                <td colspan="3" class="text-end">एकूण वापर (Used):</td>
                                <td class="text-center"><?= $sumTotal ?></td>
                                <td class="text-center"><?= $sumPresent ?></td>
                                <?php foreach (array_merge($main_items, $support_items) as $it) : ?>
                                    <td class="text-center text-warning"><?= number_format($itemTotals[$it['id']] ?? 0, 3) ?></td>
                                <?php endforeach; ?>
                                <td></td>
                            </tr>
                            <tr class="bg-black">
                                <td colspan="5" class="text-end fw-bold text-info">शिल्लक साठा (Remaining Stock):</td>
                                <?php foreach (array_merge($main_items, $support_items) as $it) :
                                    $rem = ($monthly_stock_logic[$it['id']]['available']) - ($itemTotals[$it['id']] ?? 0);
                                ?>
                                    <td class="text-center fw-bold text-info"><?= number_format($rem, 3) ?></td>
                                <?php endforeach; ?>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </form>
        </div>
    </div>

    <script src="<?php echo base_url('js/jquery-3.6.0.min.js'); ?>"></script>
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