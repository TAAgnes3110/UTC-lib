<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends BaseModel
{
    use HasFactory;

    protected $table = 'files';
    public static string $tableName = 'files';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'file_path',
        'mime_type',
        'size',
        'user_id',
        'fileable_type',
        'fileable_id',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fileable()
    {
        return $this->morphTo();
    }

    public function pdfNotes()
    {
        return $this->hasMany(PdfNote::class);
    }
}
