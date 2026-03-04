<?php

namespace Database\Factories;

use App\Models\ProductImage;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductImage>
 */
class ProductImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'image_url' => fake()->imageUrl(800, 600, 'products', true),
            'is_main' => false,
            'color' => fake()->optional(0.6)->colorName(),
            'size' => fake()->optional(0.5)->randomElement(['S', 'M', 'L', 'XL', 'XXL']),
        ];
    }

    /**
     * Indicate that the image is the main image.
     */
    public function main(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_main' => true,
        ]);
    }
}

