<?php


namespace App\Services\Questions\Handlers;


use App\Models\Question;
use App\Services\Questions\DTO\UpdateQuestionDTO;
use App\Services\Questions\Repositories\EloquentQuestionRepository;

class UpdateQuestionHandler
{

    private EloquentQuestionRepository $repository;

    public function __construct(EloquentQuestionRepository $eloquentQuestionRepository)
    {
        $this->repository = $eloquentQuestionRepository;
    }


    public function handle(Question $question, UpdateQuestionDTO $createQuestionDTO): Question
    {
        $question->categories()->detach();
        $this->repository
            ->updateTitle($question, $createQuestionDTO)
            ->saveCategories($question, $createQuestionDTO)
            ->updateAnswersTranslations($question, $createQuestionDTO)
            ->updateFromDTO($question, $createQuestionDTO);
        return $question;
    }



}
