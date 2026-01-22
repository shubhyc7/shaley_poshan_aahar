<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemModel extends Model
{
    protected $table      = 'items';
    protected $primaryKey = 'id';
    protected $allowedFields = ['item_name', 'item_type', 'unit', 'is_disable'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Scope helper to get only active items
    public function getActive()
    {
        return $this->where('is_disable', 0);
    }
}
