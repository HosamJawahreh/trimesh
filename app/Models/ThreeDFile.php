<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ThreeDFile extends Model
{
    use HasFactory;

    protected $table = 'three_d_files';

    protected $fillable = [
        'file_id',
        'file_path',
        'metadata_path',
        'file_name',
        'file_size',
        'mime_type',
        'expiry_time',
        'last_accessed_at',
    ];

    protected $casts = [
        'expiry_time' => 'datetime',
        'last_accessed_at' => 'datetime',
    ];

    /**
     * Check if the file is expired
     */
    public function isExpired(): bool
    {
        return now()->greaterThan($this->expiry_time);
    }

    /**
     * Get expired files query
     */
    public static function expired()
    {
        return static::where('expiry_time', '<', now());
    }

    /**
     * Update last accessed timestamp
     */
    public function markAccessed()
    {
        $this->update(['last_accessed_at' => now()]);
    }

    /**
     * Get time remaining until expiry in hours
     */
    public function getTimeRemainingAttribute(): float
    {
        if ($this->isExpired()) {
            return 0;
        }

        return now()->diffInHours($this->expiry_time, true);
    }

    /**
     * Scope to get files expiring soon (within X hours)
     */

    public function scopeExpiringSoon($query, int $hours = 24)
    {
        return $query->where('expiry_time', '>', now())
                    ->where('expiry_time', '<', now()->addHours($hours));
    }
}
