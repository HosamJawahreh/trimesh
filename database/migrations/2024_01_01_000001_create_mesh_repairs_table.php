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
        Schema::create('mesh_repairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained('three_d_files')->onDelete('cascade');
            $table->decimal('original_volume_cm3', 12, 4)->nullable();
            $table->decimal('repaired_volume_cm3', 12, 4)->nullable();
            $table->integer('holes_filled')->default(0);
            $table->decimal('quality_score', 5, 2)->default(0);
            $table->decimal('repair_time_seconds', 8, 2)->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->boolean('aggressive_mode')->default(true);
            $table->boolean('is_watertight')->default(false);
            $table->boolean('is_manifold')->default(false);
            $table->string('repaired_file_path')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('file_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mesh_repairs');
    }
};
