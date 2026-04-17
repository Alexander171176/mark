<?php

namespace Database\Factories\Admin\Blog\Tag;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin\Blog\Tag\Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->word;
        $slug = Str::slug($name);

        return [
            'name' => ucfirst($name),
            'slug' => $slug,
            'locale' => $this->faker->randomElement(['ru', 'en', 'kz']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
