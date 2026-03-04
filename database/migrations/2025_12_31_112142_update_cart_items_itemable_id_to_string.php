<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $table = config('laravel-cart.cart_items.table', 'cart_items');
        
        // Keep itemable_id as string to support polymorphic cart item keys.
        // Using raw SQL for better compatibility.
        DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `itemable_id` VARCHAR(255) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table = config('laravel-cart.cart_items.table', 'cart_items');
        
        // Revert back to unsignedBigInteger
        DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `itemable_id` UNSIGNED BIGINT NOT NULL");
    }
};
