<?php


namespace App\Http\Controllers;


use App\Models\QuestionCategory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class CommonController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $questionCategories = QuestionCategory::paginate(50);
        return view('quiz.welcome', [
            'questionCategories' => $questionCategories
        ]);
    }


}
