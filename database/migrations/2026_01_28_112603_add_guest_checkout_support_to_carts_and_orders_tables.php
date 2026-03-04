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
        // Update carts table
        Schema::table('carts', function (Blueprint $table) {
            // Make user_id nullable if not already
            $table->foreignId('user_id')->nullable()->change();
            
            // Add guest_id column
            $table->string('guest_id', 36)->nullable()->after('user_id');
            $table->index('guest_id');
        });

        // Update orders table
        Schema::table('orders', function (Blueprint $table) {
            // Add guest_id column
            $table->string('guest_id', 36)->nullable()->after('user_id');
            $table->index('guest_id');
            
            // Add guest detail columns
            $table->string('guest_name')->nullable()->after('guest_id');
            $table->string('guest_email')->nullable()->after('guest_name');
            $table->string('guest_phone', 20)->nullable()->after('guest_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['guest_id', 'guest_name', 'guest_email', 'guest_phone']);
        });

        // Revert carts table
        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex(['guest_id']);
            $table->dropColumn('guest_id');
            // Note: We don't revert user_id to NOT NULL to avoid breaking existing data
        });
    }
};
