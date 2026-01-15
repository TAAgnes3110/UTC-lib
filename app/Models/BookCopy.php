<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookCopy extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'book_copies';
    public static string $tableName = 'book_copies';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'book_id',
        'supplier_id',
        'barcode',
        'price',
        'status',
        'condition',
        'import_date',
        'params',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'import_date' => 'date',
        'params' => 'object',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function borrowItems()
    {
        return $this->hasMany(BorrowItem::class);
    }
}
