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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['shipping', 'billing'])->default('shipping');
            $table->string('country', 100);
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('company', 100)->nullable();
            $table->string('address', 255);
            $table->string('apartment', 100)->nullable();
            $table->string('city', 50);
            $table->string('postal_code', 20)->nullable();
            $table->string('phone', 20);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
