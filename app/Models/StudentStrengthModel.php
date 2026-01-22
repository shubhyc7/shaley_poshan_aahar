<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentStrengthModel extends Model
{
    protected $table      = 'student_strength';
    protected $primaryKey = 'id';
    protected $allowedFields = ['category', 'total_students', 'month', 'year', 'is_disable'];

    // Enable automatic timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Helper to get only active records
    public function getActive()
    {
        return $this->where('is_disable', 0);
    }
}
