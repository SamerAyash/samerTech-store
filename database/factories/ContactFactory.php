<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'subject' => fake()->sentence(4),
            'message' => fake()->paragraphs(3, true),
            'readed' => fake()->boolean(30), // 30% chance of being read
        ];
    }

    /**
     * Indicate that the contact message is read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'readed' => true,
        ]);
    }

    /**
     * Indicate that the contact message is unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'readed' => false,
        ]);
    }
}

