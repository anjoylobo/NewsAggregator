<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory class for generating dummy `Article` model data.
 *
 * This class uses the Faker library to generate random data for the
 * `Article` model's fields, which is useful for testing and seeding
 * the database with sample articles.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * This method returns an array with the default attributes for 
     * creating an instance of the `Article` model, such as title, 
     * description, category, source, author, published date, and URL.
     *
     * @return array<string, mixed> An array containing the default attributes for the `Article` model.
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence, // Generate a random sentence for the title
            'description' => $this->faker->paragraph, // Generate a random paragraph for the description
            'category' => $this->faker->randomElement(['Technology', 'Health', 'Finance', 'Education']), // Randomly select a category
            'source' => $this->faker->company, // Generate a random company name for the source
            'author' => $this->faker->name, // Generate a random name for the author
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'), // Generate a random published date within the last year
            'url' => $this->faker->url, // Generate a random URL
        ];
    }
}
