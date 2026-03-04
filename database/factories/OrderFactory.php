<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $shippingCost = fake()->randomFloat(2, 5, 50);
        $totalAmount = fake()->randomFloat(2, 50, 2000);
        
        return [
            'order_number' => 'ORD-' . fake()->unique()->numerify('########'),
            'user_id' => User::factory(),
            'total_amount' => $totalAmount,
            'payment_status' => fake()->randomElement(['unpaid', 'paid', 'refunded']),
            'status' => fake()->randomElement(['pending', 'processing', 'shipped', 'delivered', 'cancelled']),
            'shipping_address' => fake()->address(),
            'shipping_cost' => $shippingCost,
            'shipping_method' => fake()->randomElement(['standard', 'express', 'overnight', 'pickup']),
        ];
    }

    /**
     * Indicate that the order is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'paid',
        ]);
    }

    /**
     * Indicate that the order is delivered.
     */
    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
            'payment_status' => 'paid',
        ]);
    }
}

