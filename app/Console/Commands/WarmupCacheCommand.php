<?php

namespace App\Console\Commands;

use App\Models\Audit;
use App\Models\Project;
use App\Models\Report;
use App\Models\Utility;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class WarmupCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cache:warmup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache warm up';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Cache::put('auditsCount', Audit::all()->count(), config('cache.ttl'));
        Cache::put('reportsCount', Report::all()->count(), config('cache.ttl'));
        Cache::put('projectsCount', Project::all()->count(), config('cache.ttl'));
        Cache::put('utilitiesCount', Utility::all()->count(), config('cache.ttl'));
    }
}
