<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Report\StoreFormRequest;
use App\Http\Requests\Admin\Report\UpdateFormRequest;
use App\Models\Project;
use App\Models\Report;
use App\Models\Utility;
use App\Services\ReportService;

class ReportController extends Controller
{

    public function __construct(public readonly ReportService $reportService)
    {
        $this->authorizeResource(Report::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reports = Report::all();
        return view('admin.reports.index', compact('reports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $statuses = Report::getStatuses();
        $projects = Project::all();
        $utilities = Utility::all();
        return view('admin.reports.create', compact('statuses', 'projects', 'utilities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFormRequest $request)
    {
        $this->reportService->store($request);
        return redirect()->route('reports.index')->with('report.store', 'Создание отчета поставлено в очередь. Ожидайте.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {
        return view('admin.reports.show', compact('report'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Report $report)
    {
        $statuses = Report::getStatuses();
        $projects = Project::all();
        $utilities = Utility::all();
        return view('admin.reports.edit', compact('report', 'statuses', 'projects', 'utilities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFormRequest $request, Report $report)
    {
        $report->update($request->validated());
        return redirect()->route('reports.show', $report->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        $report->delete();
        return redirect()->route('reports.index');
    }
}
