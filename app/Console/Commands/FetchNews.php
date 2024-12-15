<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchNews extends Command
{
    protected $signature = 'fetch:news';
    protected $description = 'Fetch news articles from various APIs and store them in the database';

    public function handle()
    {
        $categories = config('custom.categories');
        $newsSources = config('custom.newsSources');

        foreach ($categories as $category) {
            foreach ($newsSources as $source => $config) {
                $this->info("Fetching {$category} news from {$source}...");
                $url = str_replace('{category}', $category, $config['url_template']);

                try {
                    $this->fetchArticles($url, $category, $config['fields'], $source);
                    sleep(2);
                } catch (\Exception $e) {
                    Log::error("Error fetching news from {$source} for category {$category}: " . $e->getMessage());
                    $this->error("Error fetching news from {$source}.");
                }
            }
        }

        $this->info('News articles fetched and stored successfully.');
    }

    private function fetchArticles(string $url, string $category, array $fields, string $source)
    {
        $response = Http::get($url);

        if ($response->successful()) {
            $articles = $this->extractArticles($response, $fields, $category, $source);

            if (!empty($articles)) {
                Article::insertOrIgnore($articles);
                $this->info("Fetched and stored " . count($articles) . " articles from {$source}.");
            }
        } else {
            Log::error("Failed to fetch data from {$source} for category {$category}. Response: " . $response->body());
            $this->error("Failed to fetch data from {$source}.");
        }
    }

    private function extractArticles($response, array $fields, $category, $source): array
    {
        $articles = [];
        $responseData = $response->json();
        // $this->info("Fetched articles: " . json_encode($responseData, JSON_PRETTY_PRINT));
        foreach ($responseData['articles'] ?? $responseData['response']['docs'] ?? [] as $item) {
            $articles[] = [
                'url' => $this->getFieldValue($item, $fields['url']),
                'title' => $this->getFieldValue($item, $fields['title']),
                'description' => $this->getFieldValue($item, $fields['description'], 'No description available'),
                'author' => $this->getFieldValue($item, $fields['author'], 'Unknown'),
                'published_at' => $this->formatDate($this->getFieldValue($item, $fields['published_at'])),
                'content' => $this->getFieldValue($item, $fields['content'], 'No content available'),
                'category' => $category,
                'source' => $source,
            ];
        }

        return $articles;
    }

    private function getFieldValue($item, ?string $field, string $default = null)
    {
        if (!$field) return $default;

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

    private function formatDate(?string $date): ?string
    {
        return $date ? Carbon::parse($date)->toDateTimeString() : null;
    }
}
