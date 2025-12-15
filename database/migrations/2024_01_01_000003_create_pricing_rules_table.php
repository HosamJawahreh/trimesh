<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->string('material')->unique();
            $table->string('technology');
            $table->string('display_name');
            $table->decimal('price_per_cm3', 10, 4)->default(0);
            $table->decimal('price_per_mm2', 10, 6)->nullable(); // Surface area pricing
            $table->decimal('minimum_price', 10, 2)->default(5.00);
            $table->decimal('setup_fee', 10, 2)->default(0);
            $table->decimal('multiplier', 5, 2)->default(1.00);
            $table->decimal('machine_hour_rate', 10, 2)->default(0);
            $table->decimal('print_speed_mm3_per_hour', 10, 2)->default(1000);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('color_hex', 7)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('material');
            $table->index('technology');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_rules');
    }
};
