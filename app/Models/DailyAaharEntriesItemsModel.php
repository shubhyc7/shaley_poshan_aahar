<?php

namespace App\Models;

use CodeIgniter\Model;

class DailyAaharEntriesItemsModel extends Model
{
    protected $table      = 'daily_aahar_entries_items';
    protected $primaryKey = 'id';
    protected $allowedFields = ['daily_aahar_entries_id', 'item_id', 'qty', 'is_disable'];

    public function getSupportEntriesByDate($date)
    {
        return $this->select('daily_aahar_entries_items.*, items.item_name, items.unit')
            ->join('items', 'items.id = daily_aahar_entries_items.support_item_id')
            ->where('entry_date', $date)
            ->where('is_disable', '0')
            ->findAll();
    }
}
