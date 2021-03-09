<?php


namespace App\Http\Controllers;

use App\Services\Questions\QuestionsService;
use App\Services\Quiz\QuizService;
use Illuminate\Http\Request;

class QuizController extends Controller
{

    private QuizService $quizService;
    private QuestionsService $questionsService;

    public function __construct(QuizService $quizService, QuestionsService $questionsService)
    {
        $this->quizService = $quizService;
        $this->questionsService = $questionsService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categoriesData = $this->questionsService->getCategoriesData();

        return view('quiz.start',[
            'categoriesData' => $categoriesData,
            'formOptions' => [
                'url' => route('quiz.run', ['locale' => app()->getLocale()]),
                'method' => 'POST'
            ],
            'pageH1' => trans('messages.questions_create'),
        ]);
    }

    public function run(Request $request)
    {
        $quiz = $this->quizService->createQuizFromArray([
            'questions_max_count' => 10,
            'category_id' => $request->get('question_category_id'),
            'quiz_type_id' => 1,
        ]);

        return view('quiz.task',[
            'theme' => $quiz->category->title()->value,
            'quiz' => $quiz,
        ]);
    }

    public function next(Request $request)
    {

    }

}
