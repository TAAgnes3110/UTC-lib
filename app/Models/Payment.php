<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends BaseModel
{
    use HasFactory;

    protected $table = 'payments';
    public static string $tableName = 'payments';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'fine_id',
        'amount',
        'payment_date',
        'payment_method',
        'transaction_id',
        'gateway_log',
        'params',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'gateway_log' => 'object',
        'params' => 'object',
    ];

    public function fine()
    {
        return $this->belongsTo(Fine::class);
    }
}
