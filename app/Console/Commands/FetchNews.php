<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class FetchNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch news articles from various APIs and store them in the database';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Define the news sources and their API URLs (assuming you have keys set up for each)
        $newsSources = [
            'newsapi' => 'https://newsapi.org/v2/top-headlines?country=us&apiKey=c99a9f8e65534ac890ef56c9ba43ab7a',
            // 'bbc' => 'https://newsapi.org/v2/top-headlines?sources=bbc-news&apiKey=YOUR_API_KEY',
            'guardian' => 'https://newsapi.org/v2/top-headlines?sources=the-guardian-uk&apiKey=7f706bdc-9848-47e3-afec-653cd4f87976',
        ];

        // Loop through each news source and fetch articles
        foreach ($newsSources as $source => $url) {
            $response = Http::get($url);
            
            if ($response->successful()) {
                $articles = $response->json()['articles'];

                foreach ($articles as $item) {
                    // Prepare the article data
                    $article = [
                        'url' => $item['url'],
                        'title' => $item['title'],
                        'description' => $item['description'],
                        'source' => $source,
                        'published_at' => Carbon::parse($item['publishedAt'])->toDateTimeString(), // Convert datetime format
                    ];

                    // \Log::info($article);

                    // Insert or update the article in the database
                    Article::updateOrCreate(
                        ['url' => $item['url']],
                        [
                            'title' => $article['title'],
                            'description' => $article['description'] ?? 'No description available',
                            'source' => $article['source'],
                            'published_at' => $article['published_at'],
                            'category' => 'general',
                            'content' => $article['content'] ?? '',
                        ]
                    );
                }
            } else {
                $this->error("Failed to fetch news from {$source}");
            }
        }

        $this->info('News articles fetched and stored successfully.');
    }
}
