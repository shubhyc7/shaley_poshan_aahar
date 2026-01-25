<?php
namespace App\Models;
use CodeIgniter\Model;

class StockModel extends Model
{
    protected $table      = 'stock_transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['item_id', 'transaction_type', 'daily_aahar_entries_item_id', 'transaction_date', 'quantity', 'remarks', 'is_disable'];
    protected $useTimestamps = true;
}