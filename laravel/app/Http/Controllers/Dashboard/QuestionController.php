<?php

namespace App\Http\Controllers\Dashboard;


use App\Models\Answer;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\Translation;
use Illuminate\Http\Request;

class QuestionController extends DashboardController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /*$question = Question::where('id',1)->first();
        $answer = $question->answers()->first();


        echo 'Id question= '.$question->id.'<br/>';

        $title = $question->title()->value;
        echo 'Title = '.$title.'<hr/>';

        $comment = $question->comment()->value;
        echo 'Comment = '. $comment.'<br/>';

        $value = $answer->text()->value;
        echo 'Id first answer= '.$answer->id.'<br/>';
        echo 'First answer = '. $value. '<br/>';*/


        $questions = Question::paginate();

        return view('dashboard.questions.index',[
           'questions' => $questions
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //@todo Консоль показывает что на этой странице 51 запрос к бд. За что??

        $categoriesData = $this->getCategoriesData();
        $statuses = $this->getStatuses();

        return view('dashboard.questions.create',[
            'categoriesData' => $categoriesData,
            'statuses' => $statuses,
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

    protected function getStatuses(): array
    {
        // @todo violates the SRP and MVC pattern
        return [
            Question::STATUS_INACTIVE => __('messages.statuses.inactive'),
            Question::STATUS_ACTIVE => __('messages.statuses.active'),
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        print_r($request->all());
        //echo 'FFFFFFFFFFFFFFF';
        dd($request);

        $exist = Question::create($request->all());

        $translationModels = $answerModels = [];

        foreach ($request->input('title') as $locale => $item) {
            $translationModels[] = new Translation([
                'locale' => $locale,
                'key' => 'title',
                'value' => $item ?? ''
            ]);
        }

        foreach ($request->input('answer') as $locale => $answers) {
            foreach ($answers as $item) {
                $answer = new Answer([
                    'right' => Answer::RIGHT_NO
                ]);
                /*$translations[] = new Translation([
                    'locale' => $locale,
                    'key' => 'title',
                    'value' => $item ?? ''
                ]);*/

                $answerModels[] = $answer;
            }
        }

        $exist->answers()->saveMany($answerModels);
        $exist->translations()->saveMany($translationModels);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function show(Question $question)
    {
        return view('questions.index',[
            'questions' => [$question]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function edit(Question $question)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Question $question)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function destroy(Question $question)
    {
        //
    }
}
