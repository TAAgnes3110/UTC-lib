<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'permissions';
    public static string $tableName = 'permissions';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'description',
        'group',
        'params',
    ];

    protected $casts = [
        'params' => 'object',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    public static function findOrCreate($name, $description = null, $group = null)
    {
        return self::firstOrCreate(
            ['name' => $name],
            [
                'description' => $description,
                'group' => $group,
            ]
        );
    }
}
