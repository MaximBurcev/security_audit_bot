<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Utility\StoreRequest;
use App\Http\Requests\Admin\Utility\UpdateRequest;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;

class UtilityController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Utility::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $utilities = Utility::all();
        return view('admin.utilities.index', compact('utilities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.utilities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        Utility::firstOrCreate($request->validated());
        return redirect()->route('utilities.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Utility $utility)
    {
        return view('admin.utilities.show', compact('utility'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Utility $utility)
    {
        return view('admin.utilities.edit', compact('utility'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Utility $utility)
    {
        $utility->update($request->validated());
        return redirect()->route('utilities.show', $utility->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Utility $utility)
    {
        $utility->delete();
        return redirect()->route('utilities.index');
    }
}
