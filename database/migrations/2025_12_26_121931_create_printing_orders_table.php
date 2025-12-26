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
        Schema::create('printing_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('quote_id')->nullable()->constrained('quotes')->onDelete('set null'); // Link to original quote
            $table->string('viewer_type'); // 'general' or 'medical'
            $table->string('viewer_link')->nullable();
            $table->decimal('total_price', 10, 2);
            $table->decimal('total_volume', 10, 2);
            $table->integer('total_files');
            $table->string('technology')->nullable(); // fdm, sla, sls, etc.
            $table->string('material')->nullable(); // pla, abs, petg, etc.
            $table->string('color')->nullable(); // hex color code
            $table->string('quality')->nullable(); // standard, high, ultra
            $table->json('files_data'); // Store all file settings
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('printing_orders');
    }
};
