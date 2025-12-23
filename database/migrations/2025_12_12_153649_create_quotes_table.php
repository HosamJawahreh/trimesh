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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number')->unique(); // QT-XXXXX

            // Customer Information
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();

            // File Information (can store multiple file IDs as JSON)
            $table->json('file_ids')->nullable(); // Array of file IDs from three_d_files table
            $table->integer('file_count')->default(0);

            // Pricing Information
            $table->decimal('total_volume_cm3', 10, 2)->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->string('material')->nullable();
            $table->string('color')->nullable();
            $table->string('quality')->nullable();
            $table->integer('quantity')->default(1);

            // Quote Details
            $table->json('pricing_breakdown')->nullable(); // Detailed per-file pricing
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();

            // Status
            $table->enum('status', ['pending', 'reviewed', 'quoted', 'accepted', 'rejected', 'completed'])->default('pending');
            $table->enum('form_type', ['general', 'medical'])->default('general');

            // Metadata
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('responded_at')->nullable();

            $table->timestamps();

            // Indexes for better performance
            $table->index('quote_number');
            $table->index('customer_email');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
