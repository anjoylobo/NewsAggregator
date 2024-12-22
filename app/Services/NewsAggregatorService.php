<?php

namespace App\Services;

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsAggregatorService
{
    // Function to fetch articles for each category and source
    public function fetchNews($categories, $newsSources)
    {
        foreach ($categories as $category) {
            foreach ($newsSources as $source => $config) {
                Log::info("Fetching {$category} news from {$source}...");
                $url = str_replace('{category}', $category, $config['url_template']);
                
                try {
                    $this->fetchArticles($url, $category, $config['fields'], $source);
                    sleep(2); // Adding sleep to prevent overloading APIs
                } catch (\Exception $e) {
                    Log::error("Error fetching news from {$source} for category {$category}: " . $e->getMessage());
                }
            }
        }
    }

    // Fetch the articles from the API
    private function fetchArticles(string $url, string $category, array $fields, string $source)
    {
        $response = Http::get($url);

        if ($response->successful()) {
            $articles = $this->extractArticles($response, $fields, $category, $source);

            if (!empty($articles)) {
                Article::insertOrIgnore($articles);
                Log::info("Fetched and stored " . count($articles) . " articles from {$source}.");
            }
        } else {
            Log::error("Failed to fetch data from {$source} for category {$category}. Response: " . $response->body());
        }
    }

    // Extract articles data from API response
    private function extractArticles($response, array $fields, $category, $source): array
    {
        $articles = [];
        $responseData = $response->json();

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

    // Retrieve field value from the nested structure
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

    // Format the date to a standard format
    private function formatDate(?string $date): ?string
    {
        return $date ? Carbon::parse($date)->toDateTimeString() : null;
    }
}
