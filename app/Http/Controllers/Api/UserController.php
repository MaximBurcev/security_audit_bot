<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return new UserCollection(User::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('user.store', $request->except(['password', 'password_confirmation']));

        return User::forceCreate([
            'name'      => $request->post('name'),
            'email'     => $request->get('email'),
            'password'  => Hash::make($request->get('password')),
            'api_token' => hash('sha256', Str::random(80)),
        ]);
    }
}
