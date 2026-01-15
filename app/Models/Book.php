<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'books';
    public static string $tableName = 'books';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'category_id',
        'publisher_id',
        'title',
        'author',
        'publication_year',
        'publication_place',
        'language',
        'number_of_pages',
        'isbn',
        'call_number',
        'description',
        'metadata',
        'cover_image',
        'is_ebook',
        'ebook_file_path',
        'price',
        'total_copies',
        'available_copies',
        'status',
        'params',
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'number_of_pages' => 'integer',
        'is_ebook' => 'boolean',
        'price' => 'decimal:2',
        'total_copies' => 'integer',
        'available_copies' => 'integer',
        'metadata' => 'object',
        'params' => 'object',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'book_author')->withPivot('order')->orderByPivot('order');
    }

    public function copies()
    {
        return $this->hasMany(BookCopy::class);
    }

    public function borrowItems()
    {
        return $this->hasManyThrough(BorrowItem::class, BookCopy::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
