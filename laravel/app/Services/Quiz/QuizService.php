<?php


namespace App\Services\Quiz;


use App\Models\Quiz;
use App\Models\User;
use App\Services\QuestionsCategories\QuestionsCategoriesService;
use App\Services\Quiz\DTO\CreateQuizDTO;
use App\Services\Quiz\Handlers\CreateQuizHandler;
use App\Services\Quiz\Repositories\EloquentQuizRepository;
use App\Services\Quiz\Repositories\EloquentQuizTypeRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class QuizService
{
    private EloquentQuizRepository $eloquentQuizRepository;
    private CreateQuizHandler $createQuizHandler;
    private QuestionsCategoriesService $questionsCategoriesService;
    private EloquentQuizTypeRepository $eloquentQuizTypeRepository;

    public function __construct(
        EloquentQuizRepository $eloquentQuizRepository,
        EloquentQuizTypeRepository $eloquentQuizTypeRepository,
        CreateQuizHandler $createQuizHandler,
        QuestionsCategoriesService $questionsCategoriesService
    )
    {
        $this->eloquentQuizRepository = $eloquentQuizRepository;
        $this->createQuizHandler = $createQuizHandler;
        $this->questionsCategoriesService = $questionsCategoriesService;
        $this->eloquentQuizTypeRepository = $eloquentQuizTypeRepository;
    }

    public function createQuizFromArray(array $array): Quiz
    {
        Log::info('QuizService create quiz',[ 'array' => $array ]);

        $dto = CreateQuizDTO::fromArray($array);
        return $this->createQuizHandler->handle($dto);
    }

    public function continueQuiz(int $quizId, User $user)
    {
        // TODO: Implement continueQuiz() method.
    }

    public function getNextQuestion($quiz, $index): bool
    {

    }

    /**
     * Получает список доступных для прохождения конкретным юзером категорий
     * @param User|null $user
     * @param string $platform
     * @return mixed
     */
    public function getAvailableCategories(string $platform, ?User $user = null): array
    {
        //@TODO: Implement real logic
        return $this->questionsCategoriesService->getCategoriesData();
    }

    public function getAvailableQuizTypes(string $platform, ?User $user = null): LengthAwarePaginator
    {
        //@TODO: Implement real logic
        return $this->eloquentQuizTypeRepository->search(1);
    }


    public function setAnswer(int $answerId)
    {
        // TODO: Implement setAnswer() method.
    }
}
