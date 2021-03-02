<?php


namespace Tests\Feature\Http\Controllers\Dashboard\QuestionCategory;


use App\Models\QuestionCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Generators\UsersGenerator;
use Tests\TestCase;
use Tests\Generators\QuestionsCategoriesGenerator;

class QuestionCategoryControllerDestroyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @group http
     * @group destroy
     * */
    public function testNotAllowed()
    {
        QuestionsCategoriesGenerator::generateEmptyCategoryQuestion();

        $item = QuestionCategory::query()->orderByDesc('id')->first();
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
        $item = QuestionsCategoriesGenerator::generateEmptyCategoryQuestion();

        $prevCount = count(QuestionCategory::all());

        $response = $this
            ->from($this->getPreviousRoute())
            ->actingAs(UsersGenerator::generateAdmin())
            ->delete($this->getRoute($item));
        $response->assertStatus(302);
        $response->assertRedirect($this->getRightFinalRoute());

        $currentCount = count(QuestionCategory::all());

        $this->assertEquals($prevCount-1, $currentCount);
    }

    protected function getRoute(QuestionCategory $item = null):string
    {
        return route('dashboard.category.destroy',['category' => $item, 'locale' => 'en']);
    }

    protected function getPreviousRoute()
    {
        return route('dashboard.category.create',['locale' => 'en']);
    }

    protected function getRightFinalRoute(?QuestionCategory $item = null)
    {
        return route('dashboard.category.index',['locale' => 'en']);
    }
}
