<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeshRepair extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_id',
        'original_volume_cm3',
        'repaired_volume_cm3',
        'holes_filled',
        'quality_score',
        'repair_time_seconds',
        'status',
        'aggressive_mode',
        'is_watertight',
        'is_manifold',
        'repaired_file_path',
        'metadata'
    ];

    protected $casts = [
        'original_volume_cm3' => 'float',
        'repaired_volume_cm3' => 'float',
        'holes_filled' => 'integer',
        'quality_score' => 'float',
        'repair_time_seconds' => 'float',
        'aggressive_mode' => 'boolean',
        'is_watertight' => 'boolean',
        'is_manifold' => 'boolean',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the file associated with this repair
     */
    public function file(): BelongsTo
    {
        return $this->belongsTo(ThreeDFile::class, 'file_id');
    }

    /**
     * Get volume change
     */
    public function getVolumeChangeAttribute(): float
    {
        return $this->repaired_volume_cm3 - $this->original_volume_cm3;
    }

    /**
     * Get volume change percentage
     */
    public function getVolumeChangePercentAttribute(): float
    {
        if ($this->original_volume_cm3 == 0) {
            return 0;
        }
        return ($this->volume_change / $this->original_volume_cm3) * 100;
    }

    /**
     * Check if repair was successful
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'completed' && $this->quality_score >= 70;
    }

    /**
     * Get quality rating
     */
    public function getQualityRatingAttribute(): string
    {
        if ($this->quality_score >= 90) return 'excellent';
        if ($this->quality_score >= 70) return 'good';
        if ($this->quality_score >= 50) return 'fair';
        return 'poor';
    }
}
