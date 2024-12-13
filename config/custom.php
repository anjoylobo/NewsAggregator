<?php

return [
    'categories' => ['business', 'entertainment', 'health', 'science', 'sports', 'technology'],
    'newsSources' => [
        'newsapi' => 'https://newsapi.org/v2/top-headlines?country=us&q={category}&apiKey=' . env('NEWSAPI_KEY'),
        'nytimes' => 'https://api.nytimes.com/svc/search/v2/articlesearch.json?q:({category})&api-key=' . env('NYTIMES_API_KEY'),
        'theguardian' => 'https://content.guardianapis.com/search?q={category}&api-key=' . env('THEGUARDIAN_API_KEY'),
    ],

];