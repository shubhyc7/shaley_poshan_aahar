<?php

namespace App\Models;

use CodeIgniter\Model;

class SupportEntryModel extends Model
{
    protected $table      = 'daily_aahar_entries_support_items';
    protected $primaryKey = 'id';
    protected $allowedFields = ['main_entry_id', 'entry_date', 'support_item_id', 'qty'];
    public function getSupportEntriesByDate($date)
    {
        return $this->select('daily_aahar_entries_support_items.*, items.item_name, items.unit')
            ->join('items', 'items.id = daily_aahar_entries_support_items.support_item_id')
            ->where('entry_date', $date)
            ->findAll();
    }
}
