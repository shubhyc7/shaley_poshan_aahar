<?php

namespace App\Models;

use CodeIgniter\Model;

class RateModel extends Model
{
    protected $table      = 'item_rates';
    protected $primaryKey = 'id';
    protected $allowedFields = ['item_id', 'category', 'per_student_qty', 'is_disable'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getRatesWithItems($filterCategory = null, $filterItemType = null)
    {
        $builder = $this->select('item_rates.*, items.item_name, items.unit, items.item_type')
            ->join('items', 'items.id = item_rates.item_id')
            ->where('item_rates.is_disable', 0);

        if (!empty($filterCategory)) {
            $builder->where('item_rates.category', $filterCategory);
        }
        if (!empty($filterItemType) && in_array($filterItemType, ['MAIN', 'SUPPORT'])) {
            $builder->where('items.item_type', $filterItemType);
        }

        return $builder->findAll();
    }
}
