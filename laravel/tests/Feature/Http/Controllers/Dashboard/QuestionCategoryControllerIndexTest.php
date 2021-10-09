<?php


namespace Tests\Feature\Http\Controllers\Dashboard;


use Tests\Generators\UsersGenerator;
use Tests\TestCase;

class QuestionCategoryControllerIndexTest extends TestCase
{

    /**
     * @group http
     * */
    public function testIndexNotAllowed()
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
    public function testIndexAdminAllowed()
    {
        $response = $this
            ->actingAs(UsersGenerator::generateAdmin())
            ->get($this->getRoute());
        $response->assertStatus(200);
    }

    protected function getRoute():string
    {
        return route('dashboard.category.index');
    }

}
