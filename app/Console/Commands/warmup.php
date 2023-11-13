<?php

namespace App\Console\Commands;

use App\Models\Audit;
use App\Models\Project;
use App\Models\Report;
use App\Models\Utility;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class warmup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:warmup';

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
        Cache::put('auditsCount', Audit::all()->count(), env('CACHE_TTL'));
        Cache::put('reportsCount', Report::all()->count(), env('CACHE_TTL'));
        Cache::put('projectsCount', Project::all()->count(), env('CACHE_TTL'));
        Cache::put('utilitiesCount', Utility::all()->count(), env('CACHE_TTL'));
    }
}
