<?php


namespace App\Services\Quiz\DTO;


class CreateQuizTypeDTO implements DTOInterface
{


    private string $title;
    private int $console;
    private int $ios;
    private int $android;
    private int $web;
    private string $answers_types;

    public function __construct(
        string $title,
        int $console,
        int $ios,
        int $android,
        int $web,
        string $answers_types
    )
    {
        $this->title = $title;
        $this->console = $console;
        $this->ios = $ios;
        $this->android = $android;
        $this->web = $web;
        $this->answers_types = $answers_types;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['title'],
            $data['console'],
            $data['ios'],
            $data['android'],
            $data['web'],
            $data['answers_types'],
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'console' => $this->console,
            'ios' => $this->ios,
            'android' => $this->android,
            'web' => $this->web,
            'answers_types' => $this->answers_types,
        ];
    }

}
