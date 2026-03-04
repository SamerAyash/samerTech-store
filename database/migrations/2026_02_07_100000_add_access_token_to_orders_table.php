<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('access_token', 64)->nullable()->unique()->after('guest_phone');
        });

        // Generate tokens for existing guest orders
        $guestOrders = \App\Models\Order::whereNull('access_token')->get();
        foreach ($guestOrders as $order) {
            $order->update(['access_token' => bin2hex(random_bytes(32))]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('access_token');
        });
    }
};
