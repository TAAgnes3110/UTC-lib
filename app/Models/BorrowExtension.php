<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class BorrowExtension extends BaseModel
{
    use HasFactory;

    protected $table = 'borrow_extensions';
    public static string $tableName = 'borrow_extensions';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'borrow_id',
        'user_id',
        'old_due_date',
        'new_due_date',
        'extension_days',
        'reason',
        'approved_by',
        'status',
        'params',
    ];

    protected $casts = [
        'old_due_date' => 'date',
        'new_due_date' => 'date',
        'extension_days' => 'integer',
        'params' => 'object',
    ];

    public function borrow()
    {
        return $this->belongsTo(Borrow::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
