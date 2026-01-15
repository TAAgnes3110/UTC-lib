<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'roles';
    public static string $tableName = 'roles';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'description',
        'status',
        'params',
    ];

    protected $casts = [
        'params' => 'object',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }
}
