<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'material',
        'technology',
        'price_per_cm3',
        'minimum_price',
        'setup_fee',
        'multiplier',
        'enabled',
    ];

    protected $casts = [
        'price_per_cm3' => 'decimal:4',
        'minimum_price' => 'decimal:2',
        'setup_fee' => 'decimal:2',
        'multiplier' => 'decimal:2',
        'enabled' => 'boolean',
    ];

    /**
     * Scope to get only enabled rules
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    /**
     * Get all unique materials
     */
    public static function getMaterials(): array
    {
        return self::enabled()
            ->distinct()
            ->pluck('material')
            ->toArray();
    }

    /**
     * Get all unique technologies
     */
    public static function getTechnologies(): array
    {
        return self::enabled()
            ->distinct()
            ->pluck('technology')
            ->toArray();
    }
}
