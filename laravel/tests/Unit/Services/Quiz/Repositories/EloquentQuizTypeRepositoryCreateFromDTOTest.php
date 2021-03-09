<?php

namespace Tests\Unit\Services\Quiz\Repositories;

use App\Models\QuizType;
use App\Services\Quiz\DTO\CreateQuizDTO;
use App\Services\Quiz\Repositories\EloquentQuizTypeRepository;
use Mockery;
use Tests\TestCase;

class EloquentQuizTypeRepositoryCreateFromDTOTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     * @group q5
     */
    public function testBasicTest()
    {
        $prevCount = count(QuizType::all());

        $dto = new CreateQuizDTO(
            'sdfds',
            10,
            1,
            0,
        );
        $testable = new EloquentQuizTypeRepository();
        $testable->createFromDTO($dto);


        $this->assertCount($prevCount + 1, QuizType::all());
    }
}
