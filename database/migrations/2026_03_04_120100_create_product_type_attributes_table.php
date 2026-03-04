<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_type_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_type_id')->constrained('product_types')->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->string('input_type')->default('text');
            $table->boolean('is_required')->default(false);
            $table->boolean('is_variant_axis')->default(false);
            $table->json('options')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['product_type_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_type_attributes');
    }
};
