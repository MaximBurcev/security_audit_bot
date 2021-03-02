<?php


namespace Tests\Feature\Http\Controllers\Dashboard\Question;


use App\Models\Question;
use Tests\Generators\UsersGenerator;
use Tests\TestCase;

class QuestionControllerStoreTest extends TestCase
{

    /**
     * @group http
     * @group store
     * */
    public function testNotAllowed()
    {
        $response = $this->get($this->getRoute());
        $response
            ->assertStatus(302)
            ->assertRedirect('/login')
        ;
    }

    /**
     * @group http
     * @group store
     * @group wd
     * */
    public function testWrongDataPassed()
    {
        $data = [];
        $response = $this
            ->from($this->getPreviousRoute())
            ->actingAs(UsersGenerator::generateAdmin())
            ->post($this->getRoute(),$data);

        $response->assertStatus(302);
        $response->assertRedirect($this->getPreviousRoute());
    }


    /**
     * @group http
     * @group store
     * */
    public function testRightDataPassed()
    {
        $prevCount = count(Question::all());
        $data = [
            'title' => [
                'en' => 'test1',
                'ru' => 'test1',
            ],
            'status' => Question::STATUS_ACTIVE
        ];
        $response = $this
            ->from($this->getPreviousRoute())
            ->actingAs(UsersGenerator::generateAdmin())
            ->post($this->getRoute(),$data);
        $response->assertStatus(302);
        $response->assertRedirect($this->getRightFinalRoute( Question::query()->orderByDesc('id')->first() ));

        $currentCount = count(Question::all());

        $this->assertEquals($prevCount+1, $currentCount);
    }

    protected function getRoute():string
    {
        return route('dashboard.question.store',['locale' => 'en']);
    }

    protected function getPreviousRoute()
    {
        return route('dashboard.question.create',['locale' => 'en']);
    }

    protected function getRightFinalRoute(Question $question)
    {
        return route('dashboard.question.edit',['question'=>$question, 'locale' => 'en']);
    }
}
