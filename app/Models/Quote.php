<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'file_ids',
        'file_count',
        'total_volume_cm3',
        'total_price',
        'material',
        'color',
        'quality',
        'quantity',
        'pricing_breakdown',
        'notes',
        'admin_notes',
        'status',
        'form_type',
        'ip_address',
        'user_agent',
        'viewed_at',
        'responded_at',
    ];

    protected $casts = [
        'file_ids' => 'array',
        'pricing_breakdown' => 'array',
        'viewed_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    /**
     * Generate unique quote number
     */
    public static function generateQuoteNumber(): string
    {
        do {
            $number = 'QT-' . strtoupper(Str::random(8));
        } while (static::where('quote_number', $number)->exists());

        return $number;
    }

    /**
     * Get related 3D files
     */
    public function threeDFiles()
    {
        if (!$this->file_ids || !is_array($this->file_ids)) {
            return collect([]);
        }

        return ThreeDFile::whereIn('file_id', $this->file_ids)->get();
    }

    /**
     * Get first file link for viewer
     */
    public function getViewerLinkAttribute(): string
    {
        if (!$this->file_ids || !is_array($this->file_ids) || count($this->file_ids) === 0) {
            return route('quote');
        }

        // Create comma-separated file IDs for multi-file support
        $filesParam = implode(',', $this->file_ids);

        return route('quote') . '?files=' . $filesParam;
    }

    /**
     * Get single file link (for backward compatibility)
     */
    public function getSingleFileLinkAttribute(): ?string
    {
        if (!$this->file_ids || !is_array($this->file_ids) || count($this->file_ids) === 0) {
            return null;
        }

        return route('quote') . '?file=' . $this->file_ids[0];
    }

    /**
     * Scope for pending quotes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for recent quotes (last 7 days)
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'reviewed' => 'info',
            'quoted' => 'primary',
            'accepted' => 'success',
            'rejected' => 'danger',
            'completed' => 'secondary',
            default => 'secondary',
        };
    }
}
