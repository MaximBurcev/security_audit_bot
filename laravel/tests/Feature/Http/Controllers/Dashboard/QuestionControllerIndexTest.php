<?php


namespace Tests\Feature\Http\Controllers\Dashboard;


use App\Models\User;
use Tests\TestCase;

class QuestionControllerIndexTest extends TestCase
{
    public function testIndexNotAllowed()
    {
        $response = $this->get(route('dashboard.question.index'));
        $response
            ->assertStatus(302)
            ->assertRedirect('/login')
        ;
    }

    public function testIndexAdminAllowed()
    {
        $response = $this
            ->actingAs($this->createAdmin())
            ->get(route('dashboard.question.index'));
        $response->assertStatus(200);
    }


    protected function createAdmin(): User
    {
        return User::factory()->create([
           'level' => User::LEVEL_ADMIN
        ]);
    }
}
