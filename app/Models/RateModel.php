<?php

namespace App\Models;

use CodeIgniter\Model;

class RateModel extends Model
{
    protected $table      = 'item_rates';
    protected $primaryKey = 'id';
    protected $allowedFields = ['item_id', 'category', 'per_student_qty', 'month', 'year'];

    // Helper to get rates with item names joined
    public function getRatesWithItems()
    {
        return $this->select('item_rates.*, items.item_name, items.unit')
            ->join('items', 'items.id = item_rates.item_id')
            ->findAll();
    }
}
