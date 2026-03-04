<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(2, true);
        $name_ar = fake()->words(2, true);
        return [
            'en' => ['name' => $name, 'slug' => Str::slug($name)],
            'ar' => ['name' => $name_ar, 'slug' => Str::slug($name_ar)],
            'status' => fake()->boolean(80), // 80% chance of being true
            'parent_id' => null, // Can be set manually when creating subcategories
        ];
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => false,
        ]);
    }

    /**
     * Indicate that the category has a parent.
     */
    public function childOf(int $parentId): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parentId,
        ]);
    }
}

