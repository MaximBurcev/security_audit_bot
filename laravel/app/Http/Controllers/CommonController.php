<?php


namespace App\Http\Controllers;


use App\Models\QuestionCategory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class CommonController extends Controller
{

    public function index()
    {
        return view('common.welcome', [
        ]);
    }


    public function about()
    {
        $questionCategories = QuestionCategory::paginate(50);
        return view('common.about', [
            'questionCategories' => $questionCategories
        ]);
    }

    public function pricing()
    {
        return view('common.pricing', [
        ]);
    }
}
