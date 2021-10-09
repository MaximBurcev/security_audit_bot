<?php


namespace Tests\Feature\Http\Controllers\Dashboard;


use App\Models\QuestionCategory;
use Tests\Generators\UsersGenerator;
use Tests\TestCase;

class QuestionCategoryControllerStoreTest extends TestCase
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
        $prevCount = count(QuestionCategory::all());
        $data = [
            //'parent_id' => null,
            'title' => [
                'en' => 'test1',
                'ru' => 'test1',
            ]
        ];
        $response = $this
            ->from($this->getPreviousRoute())
            ->actingAs(UsersGenerator::generateAdmin())
            ->post($this->getRoute(),$data);
        $response->assertStatus(302);
        $response->assertRedirect($this->getRightFinalRoute());

        $currentCount = count(QuestionCategory::all());

        $this->assertEquals($prevCount+1, $currentCount);
    }

    protected function getRoute():string
    {
        return route('dashboard.category.store');
    }

    protected function getPreviousRoute()
    {
        return route('dashboard.category.create');
    }

    protected function getRightFinalRoute()
    {
        return route('dashboard.category.create');
    }
}
