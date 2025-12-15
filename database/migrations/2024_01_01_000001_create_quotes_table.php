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
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('quote_number')->unique();
            $table->decimal('total_price', 10, 2)->default(0);
            $table->enum('status', ['pending', 'submitted', 'reviewed', 'completed', 'cancelled'])->default('pending');
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('status');
            $table->index('quote_number');
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
