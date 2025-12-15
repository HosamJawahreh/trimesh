<?php

namespace App\Services;

use App\Models\PricingRule;
use App\Models\QuoteFile;

class PricingService
{
    /**
     * Calculate price for a quote file
     *
     * @param float $volumeMm3 Volume in cubic millimeters
     * @param string $material Material name
     * @param string $technology Technology name
     * @return array Price breakdown
     */
    public function calculatePrice(float $volumeMm3, string $material, string $technology): array
    {
        // Get pricing rule
        $rule = PricingRule::where('material', $material)
            ->where('technology', $technology)
            ->where('enabled', true)
            ->first();

        if (!$rule) {
            throw new \Exception("No pricing rule found for {$material} with {$technology}");
        }

        // Convert volume from mm³ to cm³
        $volumeCm3 = $volumeMm3 / 1000;

        // Calculate base price
        $basePrice = $volumeCm3 * $rule->price_per_cm3;

        // Apply minimum price
        $price = max($basePrice, $rule->minimum_price);

        // Add setup fee
        $price += $rule->setup_fee;

        // Apply multiplier
        $finalPrice = $price * $rule->multiplier;

        return [
            'volume_mm3' => $volumeMm3,
            'volume_cm3' => round($volumeCm3, 2),
            'base_price' => round($basePrice, 2),
            'minimum_price' => $rule->minimum_price,
            'setup_fee' => $rule->setup_fee,
            'multiplier' => $rule->multiplier,
            'unit_price' => $rule->price_per_cm3,
            'calculated_price' => round($finalPrice, 2),
            'material' => $material,
            'technology' => $technology,
        ];
    }

    /**
     * Update quote file with calculated price
     *
     * @param QuoteFile $file
     * @param array $dimensions [width, height, depth] in mm
     * @param string|null $material
     * @param string|null $technology
     * @return QuoteFile
     */
    public function updateQuoteFilePrice(
        QuoteFile $file,
        array $dimensions,
        ?string $material = null,
        ?string $technology = null
    ): QuoteFile {
        // Use default material/technology if not provided
        $material = $material ?? $this->getDefaultMaterial();
        $technology = $technology ?? $this->getDefaultTechnology();

        // Calculate volume (simplified - actual STL parsing will be more accurate)
        $volumeMm3 = $file->volume_mm3 ?: ($dimensions['width'] * $dimensions['height'] * $dimensions['depth']);

        // Get price calculation
        $priceData = $this->calculatePrice($volumeMm3, $material, $technology);

        // Update file
        $file->update([
            'volume_mm3' => $priceData['volume_mm3'],
            'width_mm' => $dimensions['width'],
            'height_mm' => $dimensions['height'],
            'depth_mm' => $dimensions['depth'],
            'material' => $material,
            'technology' => $technology,
            'unit_price' => $priceData['unit_price'],
            'calculated_price' => $priceData['calculated_price'],
        ]);

        // Update quote total
        $file->quote->calculateTotalPrice();

        return $file;
    }

    /**
     * Get default material
     */
    public function getDefaultMaterial(): string
    {
        return PricingRule::enabled()
            ->orderBy('price_per_cm3')
            ->value('material') ?? 'PLA';
    }

    /**
     * Get default technology
     */
    public function getDefaultTechnology(): string
    {
        return PricingRule::enabled()
            ->orderBy('price_per_cm3')
            ->value('technology') ?? 'FDM';
    }

    /**
     * Get all available materials
     */
    public function getAvailableMaterials(): array
    {
        return PricingRule::enabled()
            ->distinct()
            ->pluck('material')
            ->toArray();
    }

    /**
     * Get all available technologies
     */
    public function getAvailableTechnologies(): array
    {
        return PricingRule::enabled()
            ->distinct()
            ->pluck('technology')
            ->toArray();
    }

    /**
     * Get all material-technology combinations
     */
    public function getAvailableCombinations(): array
    {
        return PricingRule::enabled()
            ->get()
            ->map(function ($rule) {
                return [
                    'material' => $rule->material,
                    'technology' => $rule->technology,
                    'price_per_cm3' => $rule->price_per_cm3,
                    'minimum_price' => $rule->minimum_price,
                ];
            })
            ->toArray();
    }
}
