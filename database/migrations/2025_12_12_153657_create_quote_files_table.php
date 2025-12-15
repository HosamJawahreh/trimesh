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
            $table->foreignId('quote_id')->constrained()->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->decimal('volume_mm3', 15, 2)->default(0);
            $table->decimal('width_mm', 10, 2)->default(0);
            $table->decimal('height_mm', 10, 2)->default(0);
            $table->decimal('depth_mm', 10, 2)->default(0);
            $table->string('material')->nullable();
            $table->string('technology')->nullable();
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('calculated_price', 10, 2)->default(0);
            $table->timestamps();
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
