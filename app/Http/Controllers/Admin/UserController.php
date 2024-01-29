<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreFormRequest;
use App\Http\Requests\Admin\User\UpdateFormRequest;
use App\Models\Report;
use App\Models\User;

class UserController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(User::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFormRequest $request)
    {
        User::firstOrCreate($request->validated());
        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFormRequest $request, User $user)
    {
        $user->update($request->validated());
        return redirect()->route('users.show', $user->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index');
    }

    public function reports(User $user)
    {
        $reports = [];
        foreach($user->audits as $audit) {
            foreach($audit->reports as $report) {
                $reports[] = $report;
            }
        }

        return view('admin.users.reports', compact('reports'));
    }
}
