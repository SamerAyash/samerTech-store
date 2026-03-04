<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 10);
        $price = fake()->randomFloat(2, 10, 500);
        $totalPrice = $quantity * $price;
        
        return [
            'order_id' => Order::factory(),
            'product_sku' => fake()->bothify('SKU-####-???'),
            'product_variant_id' => null,
            'product_name' => fake()->words(3, true),
            'color' => fake()->optional(0.7)->colorName(),
            'size' => (string) fake()->optional(0.6)->numberBetween(30, 50),
            'quantity' => $quantity,
            'price' => $price,
            'subtotal' => $totalPrice,
        ];
    }
}

