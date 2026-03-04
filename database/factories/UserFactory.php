<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->phoneNumber(),
            'password' => static::$password ??= Hash::make('password'),
            'gender' => fake()->randomElement(['male', 'female']),
            'birth_date' => fake()->optional()->date(),
            'country' => fake()->optional()->country(),
            'city' => fake()->optional()->city(),
            'avatar' => fake()->optional()->imageUrl(200, 200, 'people'),
            'main_address' => fake()->optional()->address(),
            'status' => fake()->randomElement(['active', 'blocked']),
            'email_verified_at' => fake()->optional(0.8)->dateTime(),
            'last_login_at' => fake()->optional(0.6)->dateTime(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
