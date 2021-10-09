<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\QuestionCategory;
use App\Models\Translation;
use Illuminate\Http\Request;

class QuestionCategoryController extends DashboardController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = QuestionCategory::paginate();

        return view('dashboard.questions_categories.index',[
            'categories' => $categories
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categoriesData = $this->getCategoriesData();

        return view('dashboard.questions_categories.create',[
            'categoriesData' => $categoriesData,
        ]);
    }

    // @todo violates the SRP and MVC pattern
    protected function getCategoriesData(): array
    {
        $data = [];
        foreach (QuestionCategory::where('question_category_id','=',null)->get() as $item){
            $item->getCategoriesTree($data, $item);
        }
        return $data;
    }


    public function store(Request $request)
    {
        // @todo Validate input
        /*$this->validate($request, [

        ]);*/

        $exist = QuestionCategory::create($request->all());

        $translationModels = [];

        foreach ($request->input('title') as $locale => $item) {
            $translationModels[] = new Translation([
                'locale' => $locale,
                'key' => 'title',
                'value' => $item ?? ''
            ]);
        }

        $exist->translations()->saveMany($translationModels);

        return redirect()->route('dashboard.category.create');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\QuestionCategory  $questionCategory
     * @return \Illuminate\Http\Response
     */
    public function show(QuestionCategory $questionCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\QuestionCategory  $questionCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(QuestionCategory $questionCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\QuestionCategory  $questionCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, QuestionCategory $questionCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\QuestionCategory  $questionCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(QuestionCategory $questionCategory)
    {
        //
    }




}
