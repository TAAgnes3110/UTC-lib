<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'categories';
    public static string $tableName = 'categories';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'code',
        'description',
        'status',
        'sort_order',
        'params',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'params' => 'object',
    ];

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
