<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserPreference;
use App\Models\User;
use App\Models\Article;

/**
 * Class UserPreferenceSeeder
 *
 * Seeder to populate the user_preferences table with user preferences.
 *
 * @package Database\Seeders
 */
class UserPreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This method will populate the user_preferences table by creating
     * 10 user preferences, each with a user-article pairing. It will fetch
     * random users and random articles and create preferences based on them.
     *
     * @return void
     */
    public function run(): void
    {
        // Get 10 random users
        $users = User::inRandomOrder()->take(10)->get();
        
        // Get 10 random articles
        $articles = Article::inRandomOrder()->take(10)->get();

        if ($users->count() > 0 && $articles->count() > 0) {
            // Create 10 user preferences, one for each user-article pair
            foreach ($users as $index => $user) {
                $article = $articles[$index]; // Get corresponding article for each user
                
                // Use the UserPreferenceFactory to create the user preference
                $this->createUserPreference($user, $article);
            }
        } else {
            echo "Not enough users or articles found to create user preferences.\n";
        }
    }

    /**
     * Create a user preference for a given user and article.
     *
     * @param User $user The user for whom the preference is created.
     * @param Article $article The article used for preference.
     *
     * @return void
     */
    private function createUserPreference(User $user, Article $article): void
    {
        UserPreference::create([
            'user_id' => $user->id,
            'preferred_sources' => [$article->source ?? 'Source A'],
            'preferred_categories' => [$article->category ?? 'Health'],
            'preferred_authors' => [$article->author ?? 'Morton Schumm'],
        ]);
    }
}

