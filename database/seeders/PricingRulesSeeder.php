<?php

namespace Database\Seeders;

use App\Models\PricingRule;
use Illuminate\Database\Seeder;

class PricingRulesSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            ['material' => 'PLA', 'technology' => 'FDM', 'price_per_cm3' => 0.15, 'minimum_price' => 5.00, 'setup_fee' => 2.00, 'multiplier' => 1.0, 'enabled' => true],
            ['material' => 'ABS', 'technology' => 'FDM', 'price_per_cm3' => 0.18, 'minimum_price' => 6.00, 'setup_fee' => 2.00, 'multiplier' => 1.0, 'enabled' => true],
            ['material' => 'PETG', 'technology' => 'FDM', 'price_per_cm3' => 0.20, 'minimum_price' => 6.50, 'setup_fee' => 2.00, 'multiplier' => 1.0, 'enabled' => true],
            ['material' => 'Resin', 'technology' => 'SLA', 'price_per_cm3' => 0.50, 'minimum_price' => 10.00, 'setup_fee' => 5.00, 'multiplier' => 1.2, 'enabled' => true],
            ['material' => 'Nylon', 'technology' => 'FDM', 'price_per_cm3' => 0.35, 'minimum_price' => 8.00, 'setup_fee' => 3.00, 'multiplier' => 1.1, 'enabled' => true],
            ['material' => 'TPU', 'technology' => 'FDM', 'price_per_cm3' => 0.40, 'minimum_price' => 9.00, 'setup_fee' => 3.00, 'multiplier' => 1.15, 'enabled' => true],
        ];

        foreach ($rules as $rule) {
            PricingRule::updateOrCreate(
                ['material' => $rule['material'], 'technology' => $rule['technology']],
                $rule
            );
        }
    }
}
