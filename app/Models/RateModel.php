<?php

namespace App\Models;

use CodeIgniter\Model;

class RateModel extends Model
{
    protected $table      = 'item_rates';
    protected $primaryKey = 'id';
    protected $allowedFields = ['item_id', 'category', 'per_student_qty', 'month', 'year', 'is_disable'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getRatesWithItems()
    {
        return $this->select('item_rates.*, items.item_name, items.unit,items.item_type')
            ->join('items', 'items.id = item_rates.item_id')
            ->where('item_rates.is_disable', 0)
            ->findAll();
    }
}
