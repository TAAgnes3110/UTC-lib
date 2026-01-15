<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibraryRule extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'library_rules';
    public static string $tableName = 'library_rules';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'user_type',
        'max_books',
        'borrow_days',
        'fine_per_day',
        'extra_config',
        'status',
        'params',
    ];

    protected $casts = [
        'max_books' => 'integer',
        'borrow_days' => 'integer',
        'fine_per_day' => 'decimal:2',
        'extra_config' => 'object',
        'params' => 'object',
    ];
}
