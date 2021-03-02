<?php


namespace Tests\Feature\Http\Controllers\Dashboard\QuestionCategory;

use App\Models\QuestionCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Generators\QuestionsCategoriesGenerator;
use Tests\Generators\UsersGenerator;
use Tests\TestCase;

class QuestionCategoryControllerUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @group http
     * @group update
     * */
    public function testNotAllowed()
    {
        $item = QuestionsCategoriesGenerator::generateEmptyCategoryQuestion();

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
        $item = QuestionsCategoriesGenerator::generateEmptyCategoryQuestion();

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
        $item = QuestionsCategoriesGenerator::generateEmptyCategoryQuestion();
        $parent = QuestionsCategoriesGenerator::generateEmptyCategoryQuestion();

        $data = [
            'title' => [
                'en' => 'new-title-en',
                'ru' => 'new-title-ru',
            ],
            'parent_id' => $parent->id
        ];
        $response = $this
            ->from($this->getPreviousRoute())
            ->actingAs(UsersGenerator::generateAdmin())
            ->patch($this->getRoute($item),$data);


        $response->assertStatus(302);
        $response->assertRedirect($this->getRightFinalRoute( $item ));

        $updatedItem = QuestionCategory::find( $item->id);

        $this->assertEquals($parent->id, $updatedItem->parent_id);
        $this->assertEquals('new-title-ru', $updatedItem->title('ru')->value);
        $this->assertEquals('new-title-en', $updatedItem->title('en')->value);
    }

    protected function getRoute($item):string
    {
        return route('dashboard.category.update',['category' => $item, 'locale' => 'en']);
    }

    protected function getPreviousRoute()
    {
        return route('dashboard.category.create',['locale' => 'en']);
    }

    protected function getRightFinalRoute(QuestionCategory $item)
    {
        return route('dashboard.category.edit',['category' => $item, 'locale' => 'en']);
    }
}
