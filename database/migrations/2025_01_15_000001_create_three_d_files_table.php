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
        Schema::create('three_d_files', function (Blueprint $table) {
            $table->id();
            $table->string('file_id')->unique()->index();
            $table->string('file_path');
            $table->string('metadata_path');
            $table->string('file_name');
            $table->bigInteger('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->timestamp('expiry_time')->index();
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamps();

            // Add index for cleanup queries
            $table->index(['expiry_time', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('three_d_files');
    }
};
