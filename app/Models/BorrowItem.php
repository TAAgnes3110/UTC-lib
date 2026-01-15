<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class BorrowItem extends BaseModel
{
    use HasFactory;

    protected $table = 'borrow_items';
    public static string $tableName = 'borrow_items';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'borrow_id',
        'book_copy_id',
        'return_date',
        'status',
        'condition_before',
        'condition_after',
        'fine_amount',
        'params',
    ];

    protected $casts = [
        'return_date' => 'date',
        'fine_amount' => 'decimal:2',
        'params' => 'object',
    ];

    public function borrow()
    {
        return $this->belongsTo(Borrow::class);
    }

    public function bookCopy()
    {
        return $this->belongsTo(BookCopy::class);
    }
}
