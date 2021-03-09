<?php

namespace App\Console\Commands\Game;

use App\Models\Question;
use App\Services\Quiz\QuizService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class RunGame extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:run  {--c|--category= : Категория вопросов}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Мини-игра на проверку знаний по php';
    private QuizService $quizService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(QuizService $quizService)
    {
        parent::__construct();
        $this->quizService = $quizService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $categoryId = $this->option('category');

        if ($categoryId === null) {
            $categories = $this->quizService->getAvailableCategories('console', Auth::user());
            $categoryId = $this->choice('Выберите категорию вопросов:', $categories);
        }

        $quizTypes = $this->quizService->getAvailableQuizTypes('console', Auth::user());
        $questionsCount = 12;

        $quiz = $this->quizService->createQuizFromArray([
            'questions_max_count' => $questionsCount,
            'category_id' => $categoryId,
            'quiz_type_id' => $quizTypes->first()->id,
        ]);


        $bar = $this->output->createProgressBar($questionsCount);
        $bar->start();

        $quiz->questions->each(function (Question $question) use ($bar){
            //@todo улучшить разметку вопросов, вынести разметку из контроллера.
            //@todo сделать сохранение результатов ответов в хранилище
            $text = $question->title()->value;
            $this->line(preg_replace('&<p>(.+?)</p>&is',"\n$1\n",$text));

            $this->choice('Выберите ответ',[
                'Помню', 'Не помню'
            ],0);
            $text = $question->answers[0]->text()->value;
            $this->line(preg_replace('&<p>(.+?)</p>&is',"\n$1\n",$text));

            $this->choice('Далее?',[''],0);
            $this->newLine(5);
            $bar->advance();
            $this->newLine(2);
        });
    }
}
