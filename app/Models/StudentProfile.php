<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentProfile extends BaseModel
{
    use HasFactory;

    protected $table = 'student_profiles';
    public static string $tableName = 'student_profiles';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'customer_id',
        'student_code',
        'class_name',
        'major',
        'student_year',
        'enrollment_date',
        'graduation_date',
        'gpa',
        'params',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'graduation_date' => 'date',
        'gpa' => 'decimal:2',
        'params' => 'object',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
