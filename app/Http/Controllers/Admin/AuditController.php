<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Audit\StoreRequest;
use App\Http\Requests\Admin\Audit\UpdateRequest;
use App\Models\Audit;
use App\Models\Project;
use App\Models\Report;
use App\Models\User;
use App\Models\Utility;

class AuditController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Audit::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $audits = Audit::all();
        return view('admin.audits.index', compact('audits'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $statuses = Report::getStatuses();
        $users = User::all();
        $reports = Report::all();

        return view('admin.audits.create', compact('statuses', 'users', 'reports'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $this->service->store($request);
        return redirect()->route('audits.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Audit $audit)
    {
        return view('admin.audits.show', compact('audit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Audit $audit)
    {
        $users = User::all();
        $reports = Report::all();
        return view('admin.audits.edit', compact('audit', 'users', 'reports'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Audit $audit)
    {
        $this->service->update($request, $audit);
        return redirect()->route('audits.show', $audit->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Audit $audit)
    {
        $audit->delete();
        return redirect()->route('audits.index');
    }
}
