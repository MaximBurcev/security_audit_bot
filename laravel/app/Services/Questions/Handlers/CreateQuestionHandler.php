<?php


namespace App\Services\Questions\Handlers;


use App\Models\Question;
use App\Services\Questions\DTO\CreateQuestionDTO;
use App\Services\Questions\Repositories\EloquentQuestionRepository;

class CreateQuestionHandler
{

    private EloquentQuestionRepository $repository;

    public function __construct(EloquentQuestionRepository $eloquentQuestionRepository)
    {
        $this->repository = $eloquentQuestionRepository;
    }


    public function handle(CreateQuestionDTO $createQuestionDTO): Question
    {
        $question = $this->repository->createFromDTO($createQuestionDTO);
        $this->repository
            ->saveTranslations($question, $createQuestionDTO)
            ->saveCategories($question, $createQuestionDTO);
        return $question;
    }



}
