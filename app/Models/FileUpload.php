<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class FileUpload extends BaseModel
{
    use HasFactory;

    protected $table = 'file_uploads';
    public static string $tableName = 'file_uploads';
    public $primaryKey = 'id';

    protected $fillable = [
        'id',
        'customer_id',
        'taxonomy',
        'user_id',
        'related_id',
        'file_name',
        'file_ext',
        'file_password',
        'file_size',
        'file_mime',
        'file_path',
        'file_url',
        'download_count',
        'ordering',
    ];

    protected $casts = [
        'customer_id' => 'integer',
        'user_id' => 'integer',
        'related_id' => 'integer',
        'file_size' => 'integer',
        'download_count' => 'integer',
        'ordering' => 'integer',
    ];

    public function scopeTaxonomy(Builder $query, ?string $taxonomy = null): void
    {
        $taxonomy = $taxonomy ?? request()->get('taxonomy');
        if ($taxonomy) {
            $query->where('taxonomy', $taxonomy);
        }
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function canDelete(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        if ($user->hasAnyRole(['SUPER_ADMIN', 'ADMIN', 'LIBRARIAN'])) {
            return true;
        }

        return $this->user_id === $user->id;
    }

    public function canEdit(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        if ($user->hasAnyRole(['SUPER_ADMIN', 'ADMIN', 'LIBRARIAN'])) {
            return true;
        }

        return $this->user_id === $user->id;
    }
}
