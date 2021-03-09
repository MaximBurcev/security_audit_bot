<?php


namespace App\Services\Quiz\Repositories;

use App\Models\Question;
use App\Models\Quiz;
use App\Services\Quiz\DTO\DTOInterface;

class EloquentQuizRepository
{
    public function createFromDTO(DTOInterface $dto): Quiz
    {
        $data = $dto->toArray();
        return Quiz::create($data);
    }

    public function addRandomQuestionsToQuiz(Quiz $quiz): self
    {
        $questions = Question::inRandomOrder()->limit($quiz->questions_max_count)->get();
        $quiz->questions()->saveMany($questions);
        return $this;
    }

}
