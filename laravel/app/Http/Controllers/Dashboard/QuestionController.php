<?php

namespace App\Http\Controllers\Dashboard;


use App\Http\Controllers\Dashboard\Requests\QuestionEditRequest;
use App\Http\Controllers\Dashboard\Requests\QuestionStoreRequest;
use App\Models\Question;
use App\Services\Questions\QuestionsService;
use Illuminate\Http\Request;

class QuestionController extends DashboardController
{
    private QuestionsService $questionsService;

    public function __construct(
        QuestionsService $questionsService
    )
    {
        $this->questionsService = $questionsService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dashboard.questions.index',[
           'questions' => $this->questionsService->searchQuestions(50, [])
        ]);
    }


    public function create(Question $question)
    {
        //@todo Консоль показывает что на этой странице 51 запрос к бд. За что??

        $categoriesData = $this->questionsService->getCategoriesData();
        $statuses = $this->questionsService->getQuestionStatuses();

        return view('dashboard.questions.edit',[
            'categoriesData' => $categoriesData,
            'question' => $question,
            'statuses' => $statuses,
            'formOptions' => [
                'url' => route('dashboard.question.store', ['locale' => app()->getLocale()]),
                'method' => 'POST'
            ],
            'pageH1' => trans('messages.questions_create'),
        ]);
    }


    public function store(QuestionStoreRequest $request)
    {
        $item = $this->questionsService->createQuestionFromArray($request->all());
        return redirect(route('dashboard.question.edit',['question' => $item, 'locale' => app()->getLocale()]));
    }


    public function show(Question $question)
    {
        return view('dashboard.questions.index',[
            'questions' => [$question]
        ]);
    }


    public function edit(Question $question)
    {
        $categoriesData = $this->questionsService->getCategoriesData();
        $statuses = $this->questionsService->getQuestionStatuses();

        return view('dashboard.questions.edit',[
            'categoriesData' => $categoriesData,
            'question' => $question,
            'statuses' => $statuses,
            'formOptions' => [
                'url' => route('dashboard.question.update',['question' => $question->id, 'locale' => app()->getLocale()]),
                'method' => 'PUT'
            ],
            'pageH1' => trans('messages.questions_edit'),
        ]);
    }


    public function update(QuestionEditRequest $request, Question $question)
    {
        $this->questionsService->updateQuestion($question, $request->all());
        return redirect(route('dashboard.question.edit', ['question' => $question, 'locale' => app()->getLocale() ]));
    }

    public function addEmptyAnswer(Request $request, Question $question)
    {
        $this->questionsService->addEmptyAnswerToQuestion($question);
        return redirect(route('dashboard.question.edit', ['question' => $question, 'locale' => app()->getLocale() ]));
    }


    public function destroy(Question $question)
    {
        $this->questionsService->destroyQuestion($question->id);
        return redirect(route('dashboard.question.index',['locale' => app()->getLocale()]));
    }
}
