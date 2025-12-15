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
        Schema::create('quote_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained('quotes')->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type', 10); // stl, obj, ply
            $table->decimal('volume_mm3', 15, 2)->nullable();
            $table->decimal('volume_cm3', 12, 2)->nullable();
            $table->decimal('width_mm', 10, 2)->nullable();
            $table->decimal('height_mm', 10, 2)->nullable();
            $table->decimal('depth_mm', 10, 2)->nullable();
            $table->decimal('surface_area_mm2', 15, 2)->nullable();
            $table->string('material')->nullable();
            $table->string('technology')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('calculated_price', 10, 2)->default(0);
            $table->text('analysis_data')->nullable(); // JSON data from geometry analysis
            $table->timestamps();
            
            $table->index('quote_id');
            $table->index('material');
            $table->index('technology');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_files');
    }
};
