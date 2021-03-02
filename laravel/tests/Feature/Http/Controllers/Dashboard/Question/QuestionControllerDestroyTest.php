<?php


namespace Tests\Feature\Http\Controllers\Dashboard\Question;


use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Generators\UsersGenerator;
use Tests\TestCase;
use Tests\Generators\QuestionsGenerator;

class QuestionControllerDestroyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @group http
     * @group destroy
     * */
    public function testNotAllowed()
    {
        $item = QuestionsGenerator::generateEmptyQuestion();

        $response = $this->delete($this->getRoute($item));

        $response
            ->assertStatus(302)
            ->assertRedirect('/login');
    }


    /**
     * @group http
     * @group destroy
     * */
    public function testDestroy()
    {
        $item = QuestionsGenerator::generateEmptyQuestion();

        $prevCount = count(Question::all());

        $response = $this
            ->from($this->getPreviousRoute())
            ->actingAs(UsersGenerator::generateAdmin())
            ->delete($this->getRoute($item));
        $response->assertStatus(302);
        $response->assertRedirect($this->getRightFinalRoute());

        $currentCount = count(Question::all());

        $this->assertEquals($prevCount-1, $currentCount);
    }

    protected function getRoute(Question $item = null):string
    {
        return route('dashboard.question.destroy',['question' => $item,'locale' => 'en']);
    }

    protected function getPreviousRoute()
    {
        return route('dashboard.question.create',['locale' => 'en']);
    }

    protected function getRightFinalRoute(?Question $item = null)
    {
        return route('dashboard.question.index',['locale' => 'en']);
    }
}
