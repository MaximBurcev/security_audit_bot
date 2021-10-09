<?php

namespace App\Http\Controllers\Dashboard;


use App\Models\Answer;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\Translation;
use App\Services\Questions\Repositories\EloquentQuestionRepository;
use App\Services\QuestionsCategories\Repositories\EloquentQuestionCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends DashboardController
{

    /**
     * @var EloquentQuestionRepository
     */
    private $eloquentQuestionRepository;
    /**
     * @var EloquentQuestionCategoryRepository
     */
    private $eloquentQuestionCategoryRepository;

    public function __construct(
        EloquentQuestionRepository $eloquentQuestionRepository,
        EloquentQuestionCategoryRepository $eloquentQuestionCategoryRepository
    )
    {
        $this->eloquentQuestionRepository = $eloquentQuestionRepository;
        $this->eloquentQuestionCategoryRepository = $eloquentQuestionCategoryRepository;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dashboard.questions.index',[
           'questions' => $this->eloquentQuestionRepository->search(50, [])
        ]);
    }


    public function create(Question $question)
    {
        //@todo Консоль показывает что на этой странице 51 запрос к бд. За что??

        $categoriesData = $this->eloquentQuestionCategoryRepository->getCategoriesData();
        $statuses = $this->eloquentQuestionRepository->getStatuses();

        return view('dashboard.questions.edit',[
            'categoriesData' => $categoriesData,
            'question' => $question,
            'statuses' => $statuses,
            'formOptions' => [
                'url' => route('dashboard.question.store'),
                'method' => 'POST'
            ],
            'pageH1' => trans('messages.questions_create'),
        ]);
    }


    public function store(Request $request)
    {
        $item = $this->eloquentQuestionRepository->store($request->all());
        return redirect(route('dashboard.question.edit',['question' => $item]));
    }


    public function show(Question $question)
    {
        return view('dashboard.questions.index',[
            'questions' => [$question]
        ]);
    }


    public function edit(Question $question)
    {
        $categoriesData = $this->eloquentQuestionCategoryRepository->getCategoriesData();
        $statuses = $this->eloquentQuestionRepository->getStatuses();

        return view('dashboard.questions.edit',[
            'categoriesData' => $categoriesData,
            'question' => $question,
            'statuses' => $statuses,
            'formOptions' => [
                'url' => route('dashboard.question.update',['question' => $question->id]),
                'method' => 'PUT'
            ],
            'pageH1' => trans('messages.questions_edit'),
        ]);
    }


    public function update(Request $request, Question $question)
    {
        $this->eloquentQuestionRepository->update($question, $request->all());
        return redirect(route('dashboard.question.edit', ['question' => $question ]));
    }

    public function addEmptyAnswer(Request $request, Question $question)
    {
        $this->eloquentQuestionRepository->addEmptyAnswer($question);
        return redirect(route('dashboard.question.edit', ['question' => $question ]));
    }


    public function destroy(Question $question)
    {
        $this->eloquentQuestionRepository->destroy($question->id);
        return redirect(route('dashboard.question.index'));
    }
}
