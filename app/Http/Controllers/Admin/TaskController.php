<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Task\StoreFormRequest;
use App\Http\Requests\Admin\Task\UpdateFormRequest;
use App\Models\Report;
use App\Models\Task;

class TaskController extends Controller
{
    //
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::all();
        return view('admin.tasks.index', compact('tasks'));
    }

    public function create()
    {
        $reports = Report::all();
        return view('admin.tasks.create', compact('reports'));
    }

    public function store(StoreFormRequest $request)
    {
        Task::firstOrCreate($request->validated());
        return redirect()->route('tasks.index');
    }

    public function show(Task $task)
    {
        return view('admin.tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $reports = Report::all();
        return view('admin.tasks.edit', compact('task', 'reports'));
    }

    public function update(UpdateFormRequest $request, Task $task)
    {
        $task->update($request->validated());
        return redirect()->route('tasks.show', $task->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index');
    }
}
