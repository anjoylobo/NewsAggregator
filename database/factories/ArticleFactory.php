<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'category' => $this->faker->randomElement(['Technology', 'Health', 'Finance', 'Education']),
            'source' => $this->faker->company,
            'author' => $this->faker->name,
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'url' => $this->faker->url,
        ];
    }
}
