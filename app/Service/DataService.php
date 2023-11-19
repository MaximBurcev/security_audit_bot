<?php

namespace App\Service;

use App\Models\Audit;
use App\Models\Project;
use App\Models\Report;
use App\Models\Utility;
use Illuminate\Support\Facades\Cache;

class DataService
{
    public function getDashboardData(): array
    {
        $auditsCount = Cache::remember('auditsCount', config('cache.ttl'), function () {
            return Audit::query()->count();
        });

        $reportsCount = Cache::remember('reportsCount', config('cache.ttl'), function () {
            return Report::query()->count();
        });

        $projectsCount = Cache::remember('projectsCount', config('cache.ttl'), function () {
            return Project::query()->count();
        });

        $utilitiesCount = Cache::remember('utilitiesCount', config('cache.ttl'), function () {
            return Utility::query()->count();
        });

        return compact('auditsCount', 'reportsCount', 'projectsCount', 'utilitiesCount');
    }
}
