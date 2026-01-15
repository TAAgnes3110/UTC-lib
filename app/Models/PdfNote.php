<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PdfNote extends BaseModel
{
    use HasFactory;

    protected $table = 'pdf_notes';
    public static string $tableName = 'pdf_notes';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'file_id',
        'user_id',
        'page_number',
        'x_position',
        'y_position',
        'content',
        'color',
    ];

    protected $casts = [
        'page_number' => 'integer',
        'x_position' => 'float',
        'y_position' => 'float',
    ];

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
