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
        Schema::table('three_d_files', function (Blueprint $table) {
            $table->enum('repair_status', ['none', 'pending', 'repaired', 'failed'])->default('none')->after('id');
            $table->boolean('is_watertight')->nullable()->after('repair_status');
            $table->timestamp('last_repair_at')->nullable()->after('is_watertight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('three_d_files', function (Blueprint $table) {
            $table->dropColumn(['repair_status', 'is_watertight', 'last_repair_at']);
        });
    }
};
