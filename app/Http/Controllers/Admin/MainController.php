<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\Project;
use App\Models\Report;
use App\Models\Utility;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index(){
        $auditsCount = Audit::all()->count();
        $reportsCount = Report::all()->count();
        $projectsCount = Project::all()->count();
        $utilitiesCount = Utility::all()->count();
        return view('admin.main.index', compact('auditsCount', 'reportsCount', 'projectsCount', 'utilitiesCount'));
    }
}
