<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuditLog extends BaseModel
{
    use HasFactory;

    protected $table = 'audit_logs';
    public static string $tableName = 'audit_logs';
    public $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'description',
        'params',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'object',
        'new_values' => 'object',
        'created_at' => 'datetime',
        'params' => 'object',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }
}
