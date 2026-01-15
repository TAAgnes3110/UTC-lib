<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'customers';
    public static string $tableName = 'customers';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'user_id',
        'user_type',
        'identity_card',
        'first_name',
        'last_name',
        'full_name',
        'phone',
        'email',
        'address',
        'birthday',
        'gender',
        'card_number',
        'department',
        'card_issue_date',
        'card_expiry_date',
        'status',
        'params',
    ];

    protected $casts = [
        'birthday' => 'date',
        'card_issue_date' => 'date',
        'card_expiry_date' => 'date',
        'params' => 'object',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($customer) {
            if (empty($customer->full_name) && !empty($customer->first_name) && !empty($customer->last_name)) {
                $customer->full_name = trim($customer->first_name . ' ' . $customer->last_name);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function staffProfile()
    {
        return $this->hasOne(StaffProfile::class);
    }

    public function borrows()
    {
        return $this->hasManyThrough(Borrow::class, User::class, 'id', 'user_id');
    }

    public function fileUploads()
    {
        return $this->hasMany(FileUpload::class);
    }

    public function isStudent(): bool
    {
        return $this->user_type === 'student';
    }

    public function isLecturer(): bool
    {
        return $this->user_type === 'lecturer';
    }

    public function isLibrarian(): bool
    {
        return $this->user_type === 'librarian';
    }

    public function isAdmin(): bool
    {
        return in_array($this->user_type, ['admin', 'superadmin']);
    }
}
