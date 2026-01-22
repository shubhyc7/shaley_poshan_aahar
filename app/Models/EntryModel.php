<?php

namespace App\Models;

use CodeIgniter\Model;

class EntryModel extends Model
{
    protected $table      = 'daily_aahar_entries';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'entry_date', 'category', 'total_students', 'present_students',
        'item_id', 'qty', 'month', 'year', 'is_disable'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
