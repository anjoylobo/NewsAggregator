<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * This method is used to seed the application's database with 
     * initial data. It calls other seeder classes such as 
     * `UserSeeder`, `ArticleSeeder`, and `UserPreferenceSeeder`
     * to populate the database with predefined records.
     *
     * @return void
     */
    public function run(): void
    {
        // Calling the individual seeders to populate the database
        $this->call([
            UserSeeder::class,
            ArticleSeeder::class,
            UserPreferenceSeeder::class,
        ]);
    }
}
