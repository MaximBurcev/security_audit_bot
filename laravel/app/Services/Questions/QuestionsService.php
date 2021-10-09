<?php


namespace App\Services\Questions;


use App\Models\Question;
use App\Services\Questions\DTO\CreateQuestionDTO;
use App\Services\Questions\DTO\UpdateQuestionDTO;
use App\Services\Questions\Handlers\CreateQuestionHandler;
use App\Services\Questions\Handlers\UpdateQuestionHandler;
use App\Services\Questions\Repositories\EloquentQuestionRepository;
use App\Services\QuestionsCategories\Repositories\EloquentQuestionCategoryRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class QuestionsService
{
    private CreateQuestionHandler $createQuestionHandler;
    private EloquentQuestionRepository $eloquentQuestionRepository;
    private EloquentQuestionCategoryRepository $eloquentQuestionCategoryRepository;
    private UpdateQuestionHandler $updateQuestionHandler;

    public function __construct(
        CreateQuestionHandler $createQuestionHandler,
        UpdateQuestionHandler $updateQuestionHandler,
        EloquentQuestionRepository $eloquentQuestionRepository,
        EloquentQuestionCategoryRepository $eloquentQuestionCategoryRepository
    )
    {
        $this->createQuestionHandler = $createQuestionHandler;
        $this->eloquentQuestionRepository = $eloquentQuestionRepository;
        $this->eloquentQuestionCategoryRepository = $eloquentQuestionCategoryRepository;
        $this->updateQuestionHandler = $updateQuestionHandler;
    }

    public function createQuestionFromArray(array $array): Question
    {
        $dto = CreateQuestionDTO::fromArray($array);
        $question = $this->createQuestionHandler->handle($dto);
        $this->eloquentQuestionRepository->addEmptyAnswer($question);
        return $question;
    }

    public function updateQuestion(Question $question, array $array): Question
    {
        $dto = UpdateQuestionDTO::fromArray($array);
        return $this->updateQuestionHandler->handle($question, $dto);
    }

    public function getCategoriesData(): array
    {
        return $this->eloquentQuestionCategoryRepository->getCategoriesData();
    }

    public function getQuestionStatuses(): array
    {
        return $this->eloquentQuestionRepository->getStatuses();
    }

    public function searchQuestions(?int $perPage = null, array $with= []): LengthAwarePaginator
    {
        return $this->eloquentQuestionRepository->search($perPage, $with);
    }

    public function addEmptyAnswerToQuestion(Question $question)
    {
        $this->eloquentQuestionRepository->addEmptyAnswer($question);
    }

    public function destroyQuestion(int $questionId)
    {
        $this->eloquentQuestionRepository->destroy($questionId);
    }
}
