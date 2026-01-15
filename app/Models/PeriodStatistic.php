<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class PeriodStatistic extends BasePeriodModel
{
    use HasFactory;

    protected $table = 'period_statistics';
    public static string $tableName = 'period_statistics';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'period',
        'stat_date',
        'stat_type',
        'total_borrows',
        'total_returns',
        'total_new_books',
        'total_new_users',
        'total_fines',
        'total_payments',
        'details',
        'params',
    ];

    protected $casts = [
        'stat_date' => 'date',
        'total_borrows' => 'integer',
        'total_returns' => 'integer',
        'total_new_books' => 'integer',
        'total_new_users' => 'integer',
        'total_fines' => 'decimal:2',
        'total_payments' => 'decimal:2',
        'details' => 'object',
        'params' => 'object',
    ];
}
