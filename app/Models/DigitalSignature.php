<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class DigitalSignature extends BaseModel
{
    use HasFactory;

    protected $table = 'digital_signatures';
    public static string $tableName = 'digital_signatures';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'user_id',
        'signable_type',
        'signable_id',
        'signature_data',
        'ip_address',
        'user_agent',
        'signed_at',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function signable()
    {
        return $this->morphTo();
    }
}
