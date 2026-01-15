<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Publisher extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'publishers';
    public static string $tableName = 'publishers';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'address',
        'email',
        'phone',
        'website',
        'description',
        'status',
        'params',
    ];

    protected $casts = [
        'params' => 'object',
    ];

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
