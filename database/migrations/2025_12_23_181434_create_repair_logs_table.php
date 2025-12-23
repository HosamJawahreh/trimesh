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
        Schema::create('repair_logs', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->text('original_file_path');
            $table->text('repaired_file_path');
            $table->integer('holes_filled')->default(0);
            $table->decimal('original_volume_cm3', 12, 4);
            $table->decimal('repaired_volume_cm3', 12, 4);
            $table->decimal('volume_change_cm3', 12, 4);
            $table->decimal('volume_change_percent', 8, 2);
            $table->integer('original_vertices');
            $table->integer('repaired_vertices');
            $table->integer('original_faces');
            $table->integer('repaired_faces');
            $table->boolean('watertight_achieved')->default(false);
            $table->string('repair_method')->default('pymeshfix');
            $table->text('repair_notes')->nullable();
            $table->timestamps();

            // Indexes for admin dashboard queries
            $table->index('created_at');
            $table->index('watertight_achieved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_logs');
    }
};
