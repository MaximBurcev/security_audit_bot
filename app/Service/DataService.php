<?php

namespace App\Service;

use App\Enums\EntityEnum;
use App\Models\Audit;
use App\Models\Project;
use App\Models\Report;
use App\Models\Task;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Support\Facades\Cache;

class DataService
{

    const SUFFIX = 'Count';

    public function getDashboardData(): array
    {
        $auditsCount = Cache::remember(EntityEnum::AUDIT->value . self::SUFFIX, config('cache.ttl'), function () {
            return Audit::query()->count();
        });

        $reportsCount = Cache::remember(EntityEnum::REPORT->value . self::SUFFIX, config('cache.ttl'), function () {
            return Report::query()->count();
        });

        $projectsCount = Cache::remember(EntityEnum::PROJECT->value . self::SUFFIX, config('cache.ttl'), function () {
            return Project::query()->count();
        });

        $utilitiesCount = Cache::remember(EntityEnum::UTILITY->value . self::SUFFIX, config('cache.ttl'), function () {
            return Utility::query()->count();
        });

        $usersCount = Cache::remember(EntityEnum::USER->value . self::SUFFIX, config('cache.ttl'), function () {
            return User::query()->count();
        });

        $tasksCount = Cache::remember(EntityEnum::TASK->value . self::SUFFIX, config('cache.ttl'), function () {
            return Task::query()->count();
        });

        return compact('auditsCount', 'reportsCount', 'projectsCount', 'utilitiesCount', 'usersCount', 'tasksCount');
    }
}
