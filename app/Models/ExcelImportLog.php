<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExcelImportLog extends BaseModel
{
    use HasFactory;

    protected $table = 'excel_import_logs';
    public static string $tableName = 'excel_import_logs';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'user_id',
        'import_type',
        'file_name',
        'file_path',
        'total_records',
        'success_count',
        'failed_count',
        'errors',
        'status',
        'started_at',
        'completed_at',
        'params',
    ];

    protected $casts = [
        'total_records' => 'integer',
        'success_count' => 'integer',
        'failed_count' => 'integer',
        'errors' => 'object',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'params' => 'object',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSuccessRateAttribute(): float
    {
        if ($this->total_records == 0) {
            return 0;
        }
        return ($this->success_count / $this->total_records) * 100;
    }
}
