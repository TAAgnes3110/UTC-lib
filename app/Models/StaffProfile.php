<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class StaffProfile extends BaseModel
{
    use HasFactory;

    protected $table = 'staff_profiles';
    public static string $tableName = 'staff_profiles';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'customer_id',
        'staff_code',
        'employee_id',
        'position',
        'academic_rank',
        'specialization',
        'hire_date',
        'contract_start_date',
        'contract_end_date',
        'contract_type',
        'params',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'params' => 'object',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
