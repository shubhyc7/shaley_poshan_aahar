<?= $this->extend('main') ?>
<?= $this->section('content') ?>
<title>शालेय पोषण आहार प्रणाली | वस्तू यादी</title>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-primary fw-bold">वस्तू यादी</h5>

        <div>
            <a href="<?= base_url('Items/export') ?>" class="btn btn-success me-2">
                <i class="fas fa-file-excel"></i> एक्सेलमध्ये निर्यात करा
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                <i class="fas fa-plus"></i> नवीन वस्तू जोडा
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
        <div class="table-responsive bg-white rounded shadow-sm border mt-4">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="table-dark">
                        <th>क्रमांक</th>
                        <th>वस्तूचे नाव</th>
                        <th>वस्तूचा प्रकार</th>
                        <th>एकक</th>
                        <th>क्रिया</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item) : ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <td><strong><?= $item['item_name'] ?></strong></td>
                            <td>
                                <span class="badge <?= $item['item_type'] == 'MAIN' ? 'bg-info' : 'bg-secondary' ?>">
                                    <?= $item['item_type'] == 'MAIN' ? 'मुख्य' : 'सहाय्यक'; ?>
                                </span>
                            </td>
                            <td><strong><?= $item['unit'] ?></strong></td>
                            <td>
                                <button type="button" class="btn btn-outline-primary btn-sm edit-btn" data-id="<?= $item['id'] ?>">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <a href="<?= base_url('items/delete/' . $item['id']) ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('ही वस्तू हटवायची?')">
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

<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('items/store') ?>" method="POST" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">नवीन वस्तू जोडा</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">वस्तूचे नाव</label>
                    <input type="text" name="item_name" class="form-control marathi_convert" placeholder="उदा. तांदूळ, मूग डाळ, मीठ" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">वस्तूचा प्रकार</label>
                    <select name="item_type" class="form-select" required>
                        <option value="MAIN">मुख्य (प्राथमिक धान्य)</option>
                        <option value="SUPPORT">सहाय्यक (मसाले/तेल/मीठ)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">एकक</label>
                    <select name="unit" class="form-select" required>
                        <option value="ग्रॅम">ग्रॅम</option>
                        <option value="किलो">किलो </option>
                        <option value="लिटर">लिटर</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">बंद करा</button>
                <button type="submit" class="btn btn-primary">जतन करा</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editItemModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editItemForm" method="POST" class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">वस्तू संपादित करा</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">वस्तूचे नाव</label>
                    <input type="text" name="item_name" id="edit_item_name" class="form-control marathi_convert" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">वस्तूचा प्रकार</label>
                    <select name="item_type" id="edit_item_type" class="form-select" required>
                        <option value="MAIN">मुख्य (प्राथमिक धान्य)</option>
                        <option value="SUPPORT">सहाय्यक (मसाले/तेल/मीठ)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">एकक</label>
                    <select name="unit" id="edit_unit" class="form-select" required>
                        <option value="ग्रॅम">ग्रॅम</option>
                        <option value="किलो">किलो</option>
                        <option value="लिटर">लिटर</option>
                    </select>
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
                url: '<?= base_url('Items/edit/') ?>/' + id,
                method: 'GET',
                success: function(data) {
                    // Set the form action dynamically to the update route
                    $('#editItemForm').attr('action', '<?= base_url('Items/update/') ?>/' + id);

                    // Populate the modal fields
                    $('#edit_item_name').val(data.item_name);
                    $('#edit_item_type').val(data.item_type);
                    $('#edit_unit').val(data.unit);

                    // Show the modal
                    $('#editItemModal').modal('show');
                },
                error: function() {
                    alert('Could not fetch item data.');
                }
            });
        });
    });

    $('.marathi_convert').on('input', function(e) {
    let $this = $(this);
    let content = $this.val();
    let cursorPosition = this.selectionStart;

    // Check if the character just entered was a space
    // On mobile, the 'input' event is triggered AFTER the space is added to the value
    let lastChar = content.substring(cursorPosition - 1, cursorPosition);

    if (lastChar === " ") {
        // Get text before the space
        let textBeforeCursor = content.substring(0, cursorPosition).trim();
        let words = textBeforeCursor.split(/\s+/);
        let lastWord = words[words.length - 1];

        // Only translate if the word contains English letters
        if (lastWord.length > 0 && /[a-zA-Z]/.test(lastWord)) {
            
            $.get('https://inputtools.google.com/request', {
                text: lastWord,
                itc: 'mr-t-i0-und',
                num: 1
            }, function(response) {
                if (response[0] === 'SUCCESS') {
                    let marathiWord = response[1][0][1][0];
                    let textAfterCursor = content.substring(cursorPosition);

                    // Reconstruct the string
                    let lastWordStart = textBeforeCursor.lastIndexOf(lastWord);
                    let prefix = textBeforeCursor.substring(0, lastWordStart);
                    
                    let newValue = prefix + marathiWord + " " + textAfterCursor;
                    $this.val(newValue);

                    // Move cursor back to the correct spot
                    let newCursorPos = prefix.length + marathiWord.length + 1;
                    $this[0].setSelectionRange(newCursorPos, newCursorPos);
                }
            });
        }
    }
});
</script>

<?= $this->endSection() ?>