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
        Schema::table('printing_orders', function (Blueprint $table) {
            $table->string('shipping_method')->nullable()->after('quality');
            $table->decimal('shipping_charge', 10, 2)->default(0)->after('shipping_method');
            $table->string('payment_method')->nullable()->after('shipping_charge');
            $table->enum('payment_status', ['pending', 'success', 'rejected', 'refund'])->default('pending')->after('payment_method');
            $table->json('payment_details')->nullable()->after('payment_status');
            $table->text('customer_note')->nullable()->after('payment_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('printing_orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_method',
                'shipping_charge',
                'payment_method',
                'payment_status',
                'payment_details',
                'customer_note'
            ]);
        });
    }
};
