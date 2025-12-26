<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change the status column to include new statuses
        DB::statement("ALTER TABLE printing_orders MODIFY COLUMN status ENUM('pending', 'processing', 'printing', 'out_for_delivery', 'delivered', 'completed', 'cancelled', 'on_hold') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original statuses
        DB::statement("ALTER TABLE printing_orders MODIFY COLUMN status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending'");
    }
};
