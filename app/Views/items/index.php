<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Items Master</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
        <i class="fas fa-plus"></i> Add New Item
    </button>
</div>

<?php if (session()->getFlashdata('status')) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('status') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Unit</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item) : ?>
                    <tr>
                        <td><?= $item['id'] ?></td>
                        <td><strong><?= $item['item_name'] ?></strong></td>
                        <td>
                            <span class="badge <?= $item['item_type'] == 'MAIN' ? 'bg-info' : 'bg-secondary' ?>">
                                <?= $item['item_type'] ?>
                            </span>
                        </td>
                        <td><?= $item['unit'] ?></td>
                        <td>
                            <button type="button" class="btn btn-outline-primary btn-sm edit-btn" data-id="<?= $item['id'] ?>">
                                <i class="fas fa-edit"></i>
                            </button>

                            <a href="<?= base_url('items/delete/' . $item['id']) ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this item?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('items/store') ?>" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Ingredient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Item Name</label>
                    <input type="text" name="item_name" class="form-control" placeholder="e.g. Rice, Moong Dal, Salt" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Item Type</label>
                    <select name="item_type" class="form-select" required>
                        <option value="MAIN">MAIN (Primary Grain)</option>
                        <option value="SUPPORT">SUPPORT (Spices/Oil/Salt)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Unit</label>
                    <select name="unit" class="form-select" required>
                        <option value="kg">kg (Kilogram)</option>
                        <option value="gm">gm (Gram)</option>
                        <option value="ltr">ltr (Liter)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save Item</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editItemModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editItemForm" method="POST" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Ingredient</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Item Name</label>
                    <input type="text" name="item_name" id="edit_item_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Item Type</label>
                    <select name="item_type" id="edit_item_type" class="form-select" required>
                        <option value="MAIN">MAIN (Primary Grain)</option>
                        <option value="SUPPORT">SUPPORT (Spices/Oil/Salt)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Unit</label>
                    <select name="unit" id="edit_unit" class="form-select" required>
                        <option value="kg">kg</option>
                        <option value="gm">gm</option>
                        <option value="ltr">ltr</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Item</button>
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
</script>

<?= $this->endSection() ?>