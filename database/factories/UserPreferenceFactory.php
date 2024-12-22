<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserPreference>
 */
class UserPreferenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = \App\Models\UserPreference::class;

    public function definition(): array
    {
        return [
            'user_id' => $this->faker->unique()->randomNumber(5),
            'preferred_sources' => $this->faker->randomElements(
                ['Source A', 'Source B', 'Source C', 'Source D'],
                $this->faker->numberBetween(1, 4)
            ),
            'preferred_categories' => $this->faker->randomElements(
                ['Technology', 'Health', 'Finance', 'Education'],
                $this->faker->numberBetween(1, 4)
            ),
            'preferred_authors' => $this->faker->randomElements(
                [$this->faker->name, $this->faker->name, $this->faker->name],
                $this->faker->numberBetween(1, 3)
            ),
        ];
    }
}
