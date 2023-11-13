<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\Project;
use App\Models\Report;
use App\Models\User;
use App\Models\Utility;
use App\Service\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MainController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(User::class);
    }

    public function index(){
        $auditsCount = Cache::remember('auditsCount', env('CACHE_TTL'), function () {
            return Audit::all()->count();
        });

        $reportsCount = Cache::remember('reportsCount', env('CACHE_TTL'), function () {
            return Report::all()->count();
        });

        $projectsCount = Cache::remember('projectsCount', env('CACHE_TTL'), function () {
            return Project::all()->count();
        });

        $utilitiesCount = Cache::remember('utilitiesCount', env('CACHE_TTL'), function () {
            return Utility::all()->count();
        });

        return view('admin.main.index', compact('auditsCount', 'reportsCount', 'projectsCount', 'utilitiesCount'));
    }
}
