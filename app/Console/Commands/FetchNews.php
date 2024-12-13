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
            foreach ($newsSources as $source => $config) {
                $this->info("Fetching {$category} news from {$source}...");
                $url = str_replace('{category}', $category, $config['url_template']);
                $this->fetchArticles($url, $category, $config['fields'], $source);
            }
        }

        $this->info('News articles fetched and stored successfully.');
    }

    /**
     * Fetch and save articles from the specified URL using the provided source's config.
     *
     * @param string $url
     * @param string $category
     * @param array $fields
     * @param string $source
     * @return void
     */
    private function fetchArticles(string $url, string $category, array $fields, string $source)
    {
        $response = Http::get($url);

        if ($response->successful()) {
            $articles = $this->extractArticles($response, $fields);
            foreach ($articles as $item) {
                $this->saveArticle(array_merge($item, ['category' => $category, 'source' => $source]));
            }
        } else {
            $this->error("Failed to fetch data from {$source} for category: {$category}");
        }
    }

    /**
     * Extract the articles from the API response based on the source's field mapping.
     *
     * @param \Illuminate\Http\Client\Response $response
     * @param array $fields
     * @return array
     */
    private function extractArticles($response, array $fields)
    {
        $articles = [];
        $responseData = $response->json();

        // Extract the articles based on the source's field mapping
        foreach ($responseData['articles'] ?? [] as $item) {
            $article = [
                'url' => $this->getFieldValue($item, $fields['url']),
                'title' => $this->getFieldValue($item, $fields['title']),
                'description' => $this->getFieldValue($item, $fields['description'], 'No description available'),
                'author' => $this->getFieldValue($item, $fields['author'], 'Unknown'),
                'published_at' => Carbon::parse($this->getFieldValue($item, $fields['published_at']))->toDateTimeString(),
                'content' => $this->getFieldValue($item, $fields['content'], ''),
            ];
            $articles[] = $article;
        }

        return $articles;
    }

    /**
     * Get the value of a field from the article, or return the default value if the field is not set.
     *
     * @param array $item
     * @param string|null $field
     * @param string $default
     * @return string|null
     */
    private function getFieldValue($item, ?string $field, string $default = null)
    {
        if ($field === null) {
            return $default;
        }

        $keys = explode('.', $field);
        $value = $item;

        foreach ($keys as $key) {
            if (isset($value[$key])) {
                $value = $value[$key];
            } else {
                return $default;
            }
        }

        return $value;
    }

    /**
     * Save an article to the database.
     *
     * @param array $data
     * @return void
     */
    private function saveArticle(array $article)
    {
        // Attempt to find or create the article
        Article::firstOrCreate(
            ['url' => $article['url']],
            [
                'title' => $article['title'],
                'description' => $article['description'],
                'author' => $article['author'],
                'published_at' => $article['published_at'],
                'content' => $article['content'],
                'category' => $article['category'],
                'source' => $article['source'],
            ]
        );
    }
}
