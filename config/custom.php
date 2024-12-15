<?php

return [
    'categories' => ['business', 'entertainment', 'health', 'science', 'sports', 'technology'],
    'newsSources' => [
        'newsapi' => [
            'url_template' => 'https://newsapi.org/v2/top-headlines?q={category}&apiKey=' . env('NEWSAPI_KEY'),
            'fields' => [
                'url' => 'url',
                'title' => 'title',
                'description' => 'description',
                'author' => 'author',
                'published_at' => 'publishedAt',
                'content' => 'content',
            ],
        ],
        'nytimes' => [
            'url_template' => 'https://api.nytimes.com/svc/search/v2/articlesearch.json?q={category}&api-key='. env('NYTIMES_API_KEY'),
            'fields' => [
                'url' => 'web_url',
                'title' => 'headline.main',
                'description' => 'abstract',
                'author' => 'byline.original',
                'published_at' => 'pub_date',
                'content' => 'lead_paragraph',
            ],
        ],
        'theguardian' => [
            'url_template' => 'https://content.guardianapis.com/search?q={category}&api-key='. env('THEGUARDIAN_API_KEY'),
            'fields' => [
                'url' => 'webUrl',
                'title' => 'webTitle',
                'description' => null,
                'author' => null,
                'published_at' => 'webPublicationDate',
                'content' => null,
            ],
        ],
    ],

];