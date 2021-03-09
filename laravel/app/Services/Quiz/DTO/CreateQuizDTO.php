<?php


namespace App\Services\Quiz\DTO;


class CreateQuizDTO implements DTOInterface
{


    private string $title;
    private int $questions_max_count;
    private int $category_id;
    private int $quiz_type_id;

    public function __construct(
        ?string $title,
        int $questions_max_count,
        int $category_id,
        int $quiz_type_id
    )
    {
        $this->title = $title;
        $this->questions_max_count = $questions_max_count;
        $this->category_id = $category_id;
        $this->quiz_type_id = $quiz_type_id;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['title'] ?? '',
            $data['questions_max_count'],
            $data['category_id'],
            $data['quiz_type_id'],
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'questions_max_count' => $this->questions_max_count,
            'category_id' => $this->category_id,
            'quiz_type_id' => $this->quiz_type_id,
        ];
    }

}
