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
        Schema::create('slider_products', function (Blueprint $table) {
            $table->id();
            $table->string('slider_type')->index();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->timestamps();

            // Ensure unique product per slider type
            $table->unique(['slider_type', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slider_products');
    }
};
