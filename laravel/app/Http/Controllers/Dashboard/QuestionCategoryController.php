<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Dashboard\Requests\QuestionCategoryStoreRequest;
use App\Models\QuestionCategory;
use App\Services\Questions\Repositories\EloquentQuestionRepository;
use App\Services\QuestionsCategories\Repositories\EloquentQuestionCategoryRepository;
use Illuminate\Http\Request;

class QuestionCategoryController extends DashboardController
{

    /**
     * @var EloquentQuestionRepository
     */
    private $eloquentQuestionRepository;
    /**
     * @var EloquentQuestionCategoryRepository
     */
    private $eloquentQuestionCategoryRepository;

    public function __construct(EloquentQuestionRepository $eloquentQuestionRepository, EloquentQuestionCategoryRepository $eloquentQuestionCategoryRepository)
    {
        $this->eloquentQuestionRepository = $eloquentQuestionRepository;
        $this->eloquentQuestionCategoryRepository = $eloquentQuestionCategoryRepository;
    }

    public function index()
    {
        return view('dashboard.questions_categories.index',[
            'categories' => $this->eloquentQuestionCategoryRepository->search()
        ]);
    }


    public function create(QuestionCategory $category)
    {
        return view('dashboard.questions_categories.edit',[
            'categoriesData' => $this->eloquentQuestionCategoryRepository->getCategoriesData(),
            'category' => $category,
            'formOptions' => [
                'url' => route('dashboard.category.store'),
                'method' => 'POST'
            ],
            'pageH1' => trans('messages.questions_categories_create'),
        ]);
    }


    public function store(QuestionCategoryStoreRequest $request)
    {
        $this->eloquentQuestionCategoryRepository->store($request->all());
        return redirect()->route('dashboard.category.create');
    }


    public function show(QuestionCategory $category)
    {
        return view('dashboard.questions_categories.index',[
            'questions' => [$category]
        ]);
    }


    public function edit(QuestionCategory $category)
    {
        $categoriesData = $this->eloquentQuestionCategoryRepository->getCategoriesData();

        return view('dashboard.questions_categories.edit',[
            'categoriesData' => $categoriesData,
            'category' => $category,
            'formOptions' => [
                'url' => route('dashboard.category.update',['category' => $category->id]),
                'method' => 'PUT'
            ],
            'pageH1' => trans('messages.questions_categories_edit'),
        ]);
    }


    public function update(Request $request, QuestionCategory $category)
    {
        $this->eloquentQuestionCategoryRepository->update($category, $request->all());
        return redirect(route('dashboard.category.edit', ['category' => $category ]));
    }


    public function destroy(QuestionCategory $category)
    {
        $this->eloquentQuestionCategoryRepository->destroy($category->id);
        return redirect(route('dashboard.category.index'));
    }




}
