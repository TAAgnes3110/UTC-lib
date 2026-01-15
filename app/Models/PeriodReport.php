<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PeriodReport extends BasePeriodModel
{
    use HasFactory;

    protected $table = 'period_reports';
    public static string $tableName = 'period_reports';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'period',
        'report_type',
        'title',
        'description',
        'data',
        'start_date',
        'end_date',
        'created_by',
        'status',
        'params',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'data' => 'object',
        'params' => 'object',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
