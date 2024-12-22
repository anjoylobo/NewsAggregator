<?php

return [
    // Defines the available categories for news.
    'categories' => ['business', 'entertainment', 'health', 'science', 'sports', 'technology'],
    
    // Configurations for different news sources.
    'newsSources' => [
        'newsapi' => [
            // URL template for the NewsAPI.
            'url_template' => 'https://newsapi.org/v2/top-headlines?q={category}&apiKey=' . env('NEWSAPI_KEY'),
            
            // Fields in the response to map to our internal keys.
            'fields' => [
                'url' => 'url',                  // URL of the article.
                'title' => 'title',              // Title of the article.
                'description' => 'description',  // Description of the article.
                'author' => 'author',            // Author of the article.
                'published_at' => 'publishedAt', // Publication date.
                'content' => 'content',          // Content of the article.
            ],
        ],
        
        'nytimes' => [
            // URL template for the New York Times API.
            'url_template' => 'https://api.nytimes.com/svc/search/v2/articlesearch.json?q={category}&api-key='. env('NYTIMES_API_KEY'),
            
            // Fields in the response to map to our internal keys.
            'fields' => [
                'url' => 'web_url',             // URL of the article.
                'title' => 'headline.main',     // Title of the article.
                'description' => 'abstract',    // Description of the article.
                'author' => 'byline.original',  // Author of the article.
                'published_at' => 'pub_date',   // Publication date.
                'content' => 'lead_paragraph',  // Content of the article.
            ],
        ],

        'theguardian' => [
            // URL template for The Guardian API.
            'url_template' => 'https://content.guardianapis.com/search?q={category}&api-key='. env('THEGUARDIAN_API_KEY'),
            
            // Fields in the response to map to our internal keys.
            'fields' => [
                'url' => 'webUrl',                  // URL of the article.
                'title' => 'webTitle',              // Title of the article.
                'description' => null,              // No description available.
                'author' => null,                   // No author field available.
                'published_at' => 'webPublicationDate', // Publication date.
                'content' => null,                  // No content field available.
            ],
        ],
    ],
];
