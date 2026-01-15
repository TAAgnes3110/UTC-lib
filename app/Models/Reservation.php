<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends BaseModel
{
    use HasFactory;

    protected $table = 'reservations';
    public static string $tableName = 'reservations';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'user_id',
        'book_id',
        'status',
        'reserved_at',
        'expires_at',
        'notified_at',
        'cancelled_at',
        'cancelled_by',
        'note',
        'params',
    ];

    protected $casts = [
        'reserved_at' => 'datetime',
        'expires_at' => 'datetime',
        'notified_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'params' => 'object',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function canceller()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isActive(): bool
    {
        return $this->status === 'pending' && !$this->isExpired();
    }
}
