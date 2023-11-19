<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\Project;
use App\Models\Report;
use App\Models\User;
use App\Models\Utility;
use App\Service\AuditService;
use App\Service\DataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MainController extends Controller
{

    private DataService $dataService;

    public function __construct(DataService $dataService)
    {
        $this->dataService = $dataService;
        $this->authorizeResource(User::class);
    }

    public function index(){
        $dashboardData = $this->dataService->getDashboardData();
        return view('admin.main.index', $dashboardData);
    }
}
