<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentStrengthModel extends Model
{
    protected $table      = 'student_strength';
    protected $primaryKey = 'id';
    protected $allowedFields = ['category', 'total_students', 'month', 'year'];
    protected $useTimestamps = false;
}
