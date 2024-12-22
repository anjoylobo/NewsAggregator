<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NewsAggregatorService;

class FetchNews extends Command
{
    protected $signature = 'fetch:news';
    protected $description = 'Fetch news articles from various APIs and store them in the database';

    protected $newsAggregatorService;

    // Injecting NewsAggregatorService into the command
    public function __construct(NewsAggregatorService $newsAggregatorService)
    {
        parent::__construct();
        $this->newsAggregatorService = $newsAggregatorService;
    }

    public function handle()
    {
        $categories = config('custom.categories');
        $newsSources = config('custom.newsSources');

        $this->newsAggregatorService->fetchNews($categories, $newsSources);

        $this->info('News articles fetched and stored successfully.');
    }
}
