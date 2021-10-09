<?php


namespace Tests\Feature\Http\Controllers\Dashboard\Question;


use App\Models\Answer;
use App\Models\Question;
use Tests\Generators\QuestionsGenerator;
use Tests\Generators\UsersGenerator;
use Tests\TestCase;

class QuestionControllerUpdateTest extends TestCase
{

    /**
     * @group http
     * @group update
     * */
    public function testNotAllowed()
    {
        $item = QuestionsGenerator::generateEmptyQuestion();

        $response = $this->get($this->getRoute($item));
        $response
            ->assertStatus(302)
            ->assertRedirect('/login')
        ;
    }

    /**
     * @group http
     * @group update
     * @group wd
     * */
    public function testWrongDataPassed()
    {
        $item = QuestionsGenerator::generateEmptyQuestion();

        $data = [];
        $response = $this
            ->from($this->getPreviousRoute())
            ->actingAs(UsersGenerator::generateAdmin())
            ->patch($this->getRoute($item),$data);

        $response->assertStatus(302);
        $response->assertRedirect($this->getPreviousRoute());
    }


    /**
     * @group http
     * @group update
     * */
    public function testRightDataPassed()
    {
        $item = QuestionsGenerator::generateEmptyQuestion();

        $data = [
            'title' => [
                'en' => 'new-title-en',
                'ru' => 'new-title-ru',
            ],
            'answer' => [
                [
                    'ru' => 'test-ru',
                    'en' => 'test-en',
                ]
            ],
            'status' => Question::STATUS_INACTIVE
        ];
        $response = $this
            ->from($this->getPreviousRoute())
            ->actingAs(UsersGenerator::generateAdmin())
            ->patch($this->getRoute($item),$data);


        $response->assertStatus(302);
        $response->assertRedirect($this->getRightFinalRoute( $item ));

        $updatedItem = Question::find( $item->id);

        $this->assertEquals('new-title-ru', $updatedItem->title('ru')->value);
        $this->assertEquals('new-title-en', $updatedItem->title('en')->value);
    }

    protected function getRoute($item):string
    {
        return route('dashboard.question.update',['question' => $item]);
    }

    protected function getPreviousRoute()
    {
        return route('dashboard.question.create');
    }

    protected function getRightFinalRoute(Question $item)
    {
        return route('dashboard.question.edit',['question' => $item]);
    }
}
