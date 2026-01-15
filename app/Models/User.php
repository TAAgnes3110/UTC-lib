<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';
    public static string $tableName = 'users';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'user_code',
        'name',
        'email',
        'password',
        'status',
        'params',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'integer',
        'params' => 'object',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function borrows()
    {
        return $this->hasMany(Borrow::class);
    }

    public function staffBorrows()
    {
        return $this->hasMany(Borrow::class, 'staff_id');
    }

    public function borrowExtensions()
    {
        return $this->hasMany(BorrowExtension::class);
    }

    public function fines()
    {
        return $this->hasMany(Fine::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function fileUploads()
    {
        return $this->hasMany(FileUpload::class);
    }

    public function pdfNotes()
    {
        return $this->hasMany(PdfNote::class);
    }

    public function digitalSignatures()
    {
        return $this->hasMany(DigitalSignature::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function excelImportLogs()
    {
        return $this->hasMany(ExcelImportLog::class);
    }

    public function periodReports()
    {
        return $this->hasMany(PeriodReport::class, 'created_by');
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    public function hasPermission(string $permission): bool
    {
        return $this->roles()->whereHas('permissions', function ($query) use ($permission) {
            $query->where('name', $permission);
        })->exists();
    }
}
