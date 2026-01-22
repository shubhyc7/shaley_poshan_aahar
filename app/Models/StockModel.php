<?php

namespace App\Models;

use CodeIgniter\Model;

class StockModel extends Model
{
    protected $table      = 'item_stock';
    protected $primaryKey = 'id';
    protected $allowedFields = ['item_id', 'opening_stock', 'received_stock', 'used_stock', 'remaining_stock', 'month', 'year'];

    public function getStockWithItems($month, $year)
    {
        return $this->select('item_stock.*, items.item_name, items.unit')
            ->join('items', 'items.id = item_stock.item_id')
            ->where(['month' => $month, 'year' => $year])
            ->findAll();
    }
}
