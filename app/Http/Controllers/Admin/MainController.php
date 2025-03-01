<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\DataService;

class MainController extends Controller
{

    private DataService $dataService;

    public function __construct(DataService $dataService)
    {
        $this->dataService = $dataService;
        $this->authorizeResource(User::class);
    }

    public function index()
    {
        $dashboardData = $this->dataService->getDashboardData();
        return view('admin.main.index', $dashboardData);
    }
}
