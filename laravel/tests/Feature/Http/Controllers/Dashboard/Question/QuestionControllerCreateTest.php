<?php


namespace Tests\Feature\Http\Controllers\Dashboard\Question;


use Tests\Generators\UsersGenerator;
use Tests\TestCase;

class QuestionControllerCreateTest extends TestCase
{

    /**
     * @group http
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
     * */
    public function testAdminAllowed()
    {
        $response = $this
            ->actingAs(UsersGenerator::generateAdmin())
            ->get($this->getRoute());
        $response->assertStatus(200);
    }

    protected function getRoute():string
    {
        return route('dashboard.question.create',['locale' => 'en']);
    }

}
