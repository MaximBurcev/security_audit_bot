<?php


namespace App\Services\Quiz\Handlers;


use App\Models\Quiz;
use App\Services\Quiz\DTO\DTOInterface;
use App\Services\Quiz\Repositories\EloquentQuizRepository;

class CreateQuizHandler
{
    private EloquentQuizRepository $repository;

    public function __construct(EloquentQuizRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(DTOInterface $dto): Quiz
    {
        $item = $this->repository->createFromDTO($dto);
        $this->repository->addRandomQuestionsToQuiz($item);

        return $item;
    }



}
