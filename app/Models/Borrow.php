<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Borrow extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'borrows';
    public static string $tableName = 'borrows';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'user_id',
        'staff_id',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
        'note',
        'params',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'params' => 'object',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function items()
    {
        return $this->hasMany(BorrowItem::class);
    }

    public function extensions()
    {
        return $this->hasMany(BorrowExtension::class);
    }

    public function fines()
    {
        return $this->hasMany(Fine::class);
    }
}
