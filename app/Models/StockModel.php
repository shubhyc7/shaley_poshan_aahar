<?php

namespace App\Models;

use CodeIgniter\Model;

class StockModel extends Model
{
    protected $table      = 'item_stock';
    protected $primaryKey = 'id';
    protected $allowedFields = ['item_id', 'opening_stock', 'received_stock', 'used_stock', 'remaining_stock', 'month', 'year', 'is_disable'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getStockWithItems($month, $year)
    {
        return $this->select('item_stock.*, items.item_name, items.unit')
            ->join('items', 'items.id = item_stock.item_id')
            ->where([
                'item_stock.month'      => $month,
                'item_stock.year'       => $year,
                'item_stock.is_disable' => 0
            ])
            ->findAll();
    }
}
