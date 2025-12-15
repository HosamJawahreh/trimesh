<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'file_name',
        'file_path',
        'volume_mm3',
        'width_mm',
        'height_mm',
        'depth_mm',
        'material',
        'technology',
        'unit_price',
        'calculated_price',
    ];

    protected $casts = [
        'volume_mm3' => 'decimal:2',
        'width_mm' => 'decimal:2',
        'height_mm' => 'decimal:2',
        'depth_mm' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'calculated_price' => 'decimal:2',
    ];

    /**
     * Get the quote that owns the file
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * Get volume in cm3
     */
    public function getVolumeCm3Attribute(): float
    {
        return $this->volume_mm3 / 1000;
    }
}
