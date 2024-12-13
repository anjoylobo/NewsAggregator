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
        $categories = config('custom.categories');
        $newsSources = config('custom.newsSources');

        foreach ($categories as $category) {
            foreach ($newsSources as $source => $urlTemplate) {
                $this->info("Fetching {$category} news from {$source}...");
                $url = str_replace('{category}', $category, $urlTemplate);
                switch ($source) {
                    case 'newsapi':
                        $this->fetchFromNewsApi($url, $category);
                        break;
                    case 'nytimes':
                        $this->fetchFromNyTimes($url, $category);
                        break;
                    case 'theguardian':
                        $this->fetchFromTheGuardian($url, $category);
                        break;
                    default:
                        $this->error("No handler defined for source: {$source}");
                        break;
                }
            }
        }

        $this->info('News articles fetched and stored successfully.');
    }

    /**
     * Fetch articles from NewsAPI for a specific category.
     *
     * @param string $url
     * @param string $category
     * @return void
     */
    private function fetchFromNewsApi(string $url, string $category)
    {
        $response = Http::get($url);

        if ($response->successful()) {
            $articles = $response->json()['articles'] ?? [];
            foreach ($articles as $item) {
                $this->saveArticle([
                    'url' => $item['url'],
                    'title' => $item['title'],
                    'description' => $item['description'] ?? 'No description available',
                    'source' => 'newsapi',
                    'author' => $item['author'] ?? 'Unknown',
                    'published_at' => Carbon::parse($item['publishedAt'])->toDateTimeString(),
                    'content' => $item['content'] ?? '',
                    'category' => $category,
                ]);
            }
        } else {
            $this->error('Failed to fetch data from NewsAPI for category: ' . $category);
        }
    }

    /**
     * Fetch articles from NYTimes for a specific category.
     *
     * @param string $url
     * @param string $category
     * @return void
     */
    private function fetchFromNyTimes(string $url, string $category)
    {
        $response = Http::get($url);

        if ($response->successful()) {
            $articles = $response->json()['response']['docs'] ?? [];
            foreach ($articles as $item) {
                $this->saveArticle([
                    'url' => $item['web_url'],
                    'title' => $item['headline']['main'],
                    'description' => $item['abstract'] ?? 'No description available',
                    'source' => 'nytimes',
                    'author' => $item['byline']['original'] ?? 'Unknown',
                    'published_at' => Carbon::parse($item['pub_date'])->toDateTimeString(),
                    'content' => $item['lead_paragraph'] ?? '',
                    'category' => $category,
                ]);
            }
        } else {
            $this->error('Failed to fetch data from NYTimes for category: ' . $category);
        }
    }

    /**
     * Fetch articles from The Guardian for a specific category.
     *
     * @param string $url
     * @param string $category
     * @return void
     */
    private function fetchFromTheGuardian(string $url, string $category)
    {
        $response = Http::get($url);

        if ($response->successful()) {
            $articles = $response->json()['response']['results'] ?? [];
            foreach ($articles as $item) {
                $this->saveArticle([
                    'url' => $item['webUrl'],
                    'title' => $item['webTitle'],
                    'description' => $item['fields']['trailText'] ?? 'No description available',
                    'source' => 'theguardian',
                    'author' => 'Unknown',
                    'published_at' => Carbon::parse($item['webPublicationDate'])->toDateTimeString(),
                    'content' => '',
                    'category' => $category,
                ]);
            }
        } else {
            $this->error('Failed to fetch data from The Guardian for category: ' . $category);
        }
    }

    /**
     * Save an article to the database.
     *
     * @param array $data
     * @return void
     */
    private function saveArticle(array $data)
    {
        Article::updateOrCreate(
            ['url' => $data['url']],
            [
                'title' => $data['title'],
                'description' => $data['description'],
                'source' => $data['source'],
                'published_at' => $data['published_at'],
                'category' => $data['category'],
                'content' => $data['content'],
                'author' => $data['author'],
            ]
        );

        $this->info("Article saved in category {$data['category']}: {$data['title']}");
    }
}
