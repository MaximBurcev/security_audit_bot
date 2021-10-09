<?php


namespace App\Services\QuestionsCategories;


use App\Models\QuestionCategory;
use App\Services\QuestionsCategories\DTO\CreateQuestionCategoryDTO;
use App\Services\QuestionsCategories\DTO\UpdateQuestionCategoryDTO;
use App\Services\QuestionsCategories\Handlers\CreateQuestionCategoryHandler;
use App\Services\QuestionsCategories\Handlers\UpdateQuestionCategoryHandler;
use App\Services\QuestionsCategories\Repositories\EloquentQuestionCategoryRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class QuestionsCategoriesService
{


    private EloquentQuestionCategoryRepository $eloquentQuestionCategoryRepository;
    private CreateQuestionCategoryHandler $createQuestionCategoryHandler;
    private UpdateQuestionCategoryHandler $updateQuestionCategoryHandler;

    public function __construct(
        EloquentQuestionCategoryRepository $eloquentQuestionCategoryRepository,
        CreateQuestionCategoryHandler $createQuestionCategoryHandler,
        UpdateQuestionCategoryHandler $updateQuestionCategoryHandler
    )
    {

        $this->eloquentQuestionCategoryRepository = $eloquentQuestionCategoryRepository;
        $this->createQuestionCategoryHandler = $createQuestionCategoryHandler;
        $this->updateQuestionCategoryHandler = $updateQuestionCategoryHandler;
    }

    public function searchQuestionsCategories(?int $perPage = null, array $with= []): LengthAwarePaginator
    {
        return $this->eloquentQuestionCategoryRepository->search($perPage, $with);
    }

    public function getCategoriesData(): array
    {
        return $this->eloquentQuestionCategoryRepository->getCategoriesData();
    }

    public function createQuestionCategoryFromArray(array $array): QuestionCategory
    {
        $dto = CreateQuestionCategoryDTO::fromArray($array);
        return $this->createQuestionCategoryHandler->handle($dto);
    }

    public function destroyQuestionCategory($item): ?bool
    {
        return $this->eloquentQuestionCategoryRepository->destroy($item);
    }

    public function updateQuestionCategory(QuestionCategory $category, array $array): QuestionCategory
    {
        $dto = UpdateQuestionCategoryDTO::fromArray($array);
        return $this->updateQuestionCategoryHandler->handle($category, $dto);
    }

}
