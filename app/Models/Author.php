<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Author extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'authors';
    public static string $tableName = 'authors';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'biography',
        'birth_date',
        'death_date',
        'nationality',
        'description',
        'photo',
        'status',
        'params',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'death_date' => 'date',
        'params' => 'object',
    ];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_author')->withPivot('order')->orderByPivot('order');
    }
}
