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

class MainController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(User::class);
    }

    public function index(){
        $auditsCount = Audit::count();
        $reportsCount = Report::count();
        $projectsCount = Project::count();
        $utilitiesCount = Utility::count();
        return view('admin.main.index', compact('auditsCount', 'reportsCount', 'projectsCount', 'utilitiesCount'));
    }
}
