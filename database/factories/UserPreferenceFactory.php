<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory class for generating dummy `UserPreference` model data.
 *
 * This class defines how to generate random data for the `UserPreference` model 
 * attributes, which can be used for seeding or testing purposes. It includes 
 * predefined sets of data for preferred sources, categories, and authors.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserPreference>
 */
class UserPreferenceFactory extends Factory
{
    /**
     * The model that this factory is generating data for.
     *
     * This is the model class that the factory is associated with. It tells the 
     * factory what type of model is being generated.
     *
     * @var string
     */
    protected $model = \App\Models\UserPreference::class;

    /**
     * Define the model's default state.
     *
     * This method returns an array with the default attributes for creating 
     * a new `UserPreference` model, including user ID, preferred sources, 
     * categories, and authors. The values are generated using the Faker library.
     *
     * @return array<string, mixed> The default attributes for the `UserPreference` model.
     */
    public function definition(): array
    {
        return [
            'user_id' => $this->faker->unique()->randomNumber(5),  // Generates a unique user ID.
            'preferred_sources' => $this->faker->randomElements(
                ['Source A', 'Source B', 'Source C', 'Source D'],  // Randomly selects preferred sources.
                $this->faker->numberBetween(1, 4)  // Chooses between 1 to 4 sources.
            ),
            'preferred_categories' => $this->faker->randomElements(
                ['Technology', 'Health', 'Finance', 'Education'],  // Randomly selects preferred categories.
                $this->faker->numberBetween(1, 4)  // Chooses between 1 to 4 categories.
            ),
            'preferred_authors' => $this->faker->randomElements(
                [$this->faker->name, $this->faker->name, $this->faker->name],  // Randomly selects preferred authors.
                $this->faker->numberBetween(1, 3)  // Chooses between 1 to 3 authors.
            ),
        ];
    }
}
