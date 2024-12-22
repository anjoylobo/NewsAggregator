<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Article;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This method seeds the `articles` table by creating 10 articles 
     * using the Article factory. The factory generates dummy data for 
     * the articles that are inserted into the database.
     *
     * @return void
     */
    public function run(): void
    {
        // Creating 10 articles using the Article factory
        Article::factory()->count(10)->create();
    }
}
