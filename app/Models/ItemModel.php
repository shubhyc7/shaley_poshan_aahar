<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemModel extends Model
{
    protected $table      = 'items';
    protected $primaryKey = 'id';
    protected $allowedFields = ['item_name', 'item_type', 'unit', 'is_active'];
}
