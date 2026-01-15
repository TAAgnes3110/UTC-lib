<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'suppliers';
    public static string $tableName = 'suppliers';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'status',
        'params',
    ];

    protected $casts = [
        'params' => 'object',
    ];

    public function bookCopies()
    {
        return $this->hasMany(BookCopy::class);
    }
}
