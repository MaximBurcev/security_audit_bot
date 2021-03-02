<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Dashboard\Requests\QuestionCategoryEditRequest;
use App\Http\Controllers\Dashboard\Requests\QuestionCategoryStoreRequest;
use App\Models\QuestionCategory;
use App\Services\Questions\Repositories\EloquentQuestionRepository;
use App\Services\QuestionsCategories\QuestionsCategoriesService;
use App\Services\QuestionsCategories\Repositories\EloquentQuestionCategoryRepository;
use Illuminate\Http\Request;

class QuestionCategoryController extends DashboardController
{
    private QuestionsCategoriesService $questionsCategoriesService;

    public function __construct(QuestionsCategoriesService $questionsCategoriesService)
    {
        $this->questionsCategoriesService = $questionsCategoriesService;
    }

    public function index()
    {
        return view('dashboard.questions_categories.index',[
            'categories' => $this->questionsCategoriesService->searchQuestionsCategories()
        ]);
    }


    public function create(QuestionCategory $category)
    {
        return view('dashboard.questions_categories.edit',[
            'categoriesData' => $this->questionsCategoriesService->getCategoriesData(),
            'category' => $category,
            'formOptions' => [
                'url' => route('dashboard.category.store',['locale' => app()->getLocale()]),
                'method' => 'POST'
            ],
            'pageH1' => trans('messages.questions_categories_create'),
        ]);
    }


    public function store(QuestionCategoryStoreRequest $request)
    {
        $this->questionsCategoriesService->createQuestionCategoryFromArray($request->all());
        return redirect()->route('dashboard.category.create',['locale' => app()->getLocale()]);
    }


    public function show(QuestionCategory $category)
    {
        return view('dashboard.questions_categories.index',[
            'questions' => [$category]
        ]);
    }


    public function edit(QuestionCategory $category)
    {
        $categoriesData = $this->questionsCategoriesService->getCategoriesData();

        return view('dashboard.questions_categories.edit',[
            'categoriesData' => $categoriesData,
            'category' => $category,
            'formOptions' => [
                'url' => route('dashboard.category.update',['category' => $category->id, 'locale' => app()->getLocale()]),
                'method' => 'PUT'
            ],
            'pageH1' => trans('messages.questions_categories_edit'),
        ]);
    }


    public function update(QuestionCategoryEditRequest $request, QuestionCategory $category)
    {
        $this->questionsCategoriesService->updateQuestionCategory($category, $request->all());
        return redirect(route('dashboard.category.edit', ['category' => $category, 'locale' => app()->getLocale() ]));
    }


    public function destroy(QuestionCategory $category)
    {
        $this->questionsCategoriesService->destroyQuestionCategory($category->id);
        return redirect(route('dashboard.category.index', ['locale' => app()->getLocale()]));
    }

}
