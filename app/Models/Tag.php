<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends BaseModel
{
    use HasFactory;

    protected $table = 'tags';
    public static string $tableName = 'tags';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'slug',
    ];

    public function books()
    {
        return $this->morphedByMany(Book::class, 'taggable');
    }

    public static function findOrCreateByName($name)
    {
        $slug = \Str::slug($name);
        return self::firstOrCreate(['slug' => $slug], ['name' => $name]);
    }
}
