<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class NewsAggregatorService
{
    protected $apiKeys = [
        'newsapi' => 'c99a9f8e65534ac890ef56c9ba43ab7a',
        'guardian' => '7f706bdc-9848-47e3-afec-653cd4f87976',
        // 'bbc' => 'YOUR_BBC_API_KEY',
    ];

    public function fetchNewsFromApi($source)
    {
        $cacheKey = "news_{$source}";

        return Cache::remember($cacheKey, 600, function () use ($source) {
            switch ($source) {
                case 'newsapi':
                    return $this->fetchFromNewsApi();
                case 'guardian':
                    return $this->fetchFromGuardian();
                // case 'bbc':
                //     return $this->fetchFromBBC();
                default:
                    throw new \Exception("Unknown source: {$source}");
            }
        });
    }

    private function fetchFromNewsApi()
    {
        $response = Http::get('https://newsapi.org/v2/top-headlines', [
            'apiKey' => $this->apiKeys['newsapi'],
            'country' => 'us',
        ]);

        return $response->json()['articles'];
    }

    private function fetchFromGuardian()
    {
        $response = Http::get('https://content.guardianapis.com/search', [
            'api-key' => $this->apiKeys['guardian'],
            'section' => 'world',
        ]);

        return $response->json()['response']['results'];
    }

    private function fetchFromBBC()
    {
        $response = Http::get('https://newsapi.org/v2/top-headlines', [
            'apiKey' => $this->apiKeys['bbc'],
            'sources' => 'bbc-news', // Example parameter
        ]);

        return $response->json()['articles'];
    }
}
