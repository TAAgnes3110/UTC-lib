<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fine extends BaseModel
{
    use HasFactory;

    protected $table = 'fines';
    public static string $tableName = 'fines';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'user_id',
        'borrow_id',
        'amount',
        'reason',
        'status',
        'params',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'params' => 'object',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function borrow()
    {
        return $this->belongsTo(Borrow::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getPaidAmountAttribute(): float
    {
        return $this->payments()->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->amount - $this->paid_amount;
    }

    public function isPaid(): bool
    {
        return $this->remaining_amount <= 0;
    }
}
