<?php

namespace App\Models;

use CodeIgniter\Model;

class StockModel extends Model
{
    protected $table      = 'stock_transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['item_id', 'stock_transactions', 'daily_aahar_entries_item_id', 'transaction_date', 'quantity', 'remarks', 'is_disable'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getStockWithItems($date)
    {
        return $this->select('stock_transactions.*, items.item_name, items.unit')
            ->join('items', 'items.id = stock_transactions.item_id')
            ->where([
                'stock_transactions.stock_date' => $date,
                'stock_transactions.is_disable' => 0
            ])
            ->findAll();
    }
}
