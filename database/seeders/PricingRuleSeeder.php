<?php

namespace Database\Seeders;

use App\Models\PricingRule;
use Illuminate\Database\Seeder;

class PricingRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pricingRules = [
            [
                'material' => 'pla',
                'technology' => 'FDM',
                'display_name' => 'PLA (Standard)',
                'price_per_cm3' => 0.50,
                'price_per_mm2' => 0.0001,
                'minimum_price' => 5.00,
                'setup_fee' => 2.00,
                'multiplier' => 1.00,
                'machine_hour_rate' => 10.00,
                'print_speed_mm3_per_hour' => 1000.00,
                'is_active' => true,
                'sort_order' => 1,
                'color_hex' => '#3498db',
                'description' => 'Standard PLA material - Good for prototypes and general use',
            ],
            [
                'material' => 'abs',
                'technology' => 'FDM',
                'display_name' => 'ABS (Durable)',
                'price_per_cm3' => 0.60,
                'price_per_mm2' => 0.0001,
                'minimum_price' => 6.00,
                'setup_fee' => 2.50,
                'multiplier' => 1.10,
                'machine_hour_rate' => 10.00,
                'print_speed_mm3_per_hour' => 900.00,
                'is_active' => true,
                'sort_order' => 2,
                'color_hex' => '#e74c3c',
                'description' => 'ABS plastic - Strong and durable, heat resistant',
            ],
            [
                'material' => 'petg',
                'technology' => 'FDM',
                'display_name' => 'PETG (Strong)',
                'price_per_cm3' => 0.70,
                'price_per_mm2' => 0.0001,
                'minimum_price' => 7.00,
                'setup_fee' => 3.00,
                'multiplier' => 1.15,
                'machine_hour_rate' => 10.00,
                'print_speed_mm3_per_hour' => 850.00,
                'is_active' => true,
                'sort_order' => 3,
                'color_hex' => '#2ecc71',
                'description' => 'PETG - Strong, durable, and chemical resistant',
            ],
            [
                'material' => 'nylon',
                'technology' => 'FDM',
                'display_name' => 'Nylon (Industrial)',
                'price_per_cm3' => 1.20,
                'price_per_mm2' => 0.0002,
                'minimum_price' => 10.00,
                'setup_fee' => 5.00,
                'multiplier' => 1.30,
                'machine_hour_rate' => 12.00,
                'print_speed_mm3_per_hour' => 700.00,
                'is_active' => true,
                'sort_order' => 4,
                'color_hex' => '#f39c12',
                'description' => 'Nylon - Excellent strength, flexibility, and durability',
            ],
            [
                'material' => 'tpu',
                'technology' => 'FDM',
                'display_name' => 'TPU (Flexible)',
                'price_per_cm3' => 1.50,
                'price_per_mm2' => 0.0002,
                'minimum_price' => 12.00,
                'setup_fee' => 5.00,
                'multiplier' => 1.40,
                'machine_hour_rate' => 12.00,
                'print_speed_mm3_per_hour' => 600.00,
                'is_active' => true,
                'sort_order' => 5,
                'color_hex' => '#9b59b6',
                'description' => 'TPU - Flexible and rubber-like material',
            ],
            [
                'material' => 'resin_standard',
                'technology' => 'SLA',
                'display_name' => 'Resin (Standard)',
                'price_per_cm3' => 2.00,
                'price_per_mm2' => 0.0003,
                'minimum_price' => 15.00,
                'setup_fee' => 10.00,
                'multiplier' => 1.50,
                'machine_hour_rate' => 15.00,
                'print_speed_mm3_per_hour' => 500.00,
                'is_active' => true,
                'sort_order' => 6,
                'color_hex' => '#1abc9c',
                'description' => 'SLA Resin - High detail and smooth surface finish',
            ],
            [
                'material' => 'resin_tough',
                'technology' => 'SLA',
                'display_name' => 'Resin (Tough)',
                'price_per_cm3' => 2.50,
                'price_per_mm2' => 0.0003,
                'minimum_price' => 18.00,
                'setup_fee' => 10.00,
                'multiplier' => 1.60,
                'machine_hour_rate' => 15.00,
                'print_speed_mm3_per_hour' => 500.00,
                'is_active' => true,
                'sort_order' => 7,
                'color_hex' => '#34495e',
                'description' => 'Tough Resin - Impact resistant with high detail',
            ],
            [
                'material' => 'metal_steel',
                'technology' => 'SLM',
                'display_name' => 'Stainless Steel',
                'price_per_cm3' => 5.00,
                'price_per_mm2' => 0.0005,
                'minimum_price' => 50.00,
                'setup_fee' => 25.00,
                'multiplier' => 2.00,
                'machine_hour_rate' => 50.00,
                'print_speed_mm3_per_hour' => 200.00,
                'is_active' => true,
                'sort_order' => 8,
                'color_hex' => '#95a5a6',
                'description' => 'Metal 3D printing - Industrial strength stainless steel',
            ],
        ];

        foreach ($pricingRules as $rule) {
            PricingRule::updateOrCreate(
                ['material' => $rule['material']],
                $rule
            );
        }

        $this->command->info('Pricing rules seeded successfully!');
    }
}
