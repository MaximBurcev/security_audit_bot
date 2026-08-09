<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\BotReportJob;
use App\Models\Audit;
use App\Models\Project;
use App\Models\Report;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * Смоук админки: каждый тест сам создаёт нужные записи.
 *
 * Раньше тесты брали данные из засеянной базы (`User::where(...)->first()`,
 * `Audit::all()->random()`) без `RefreshDatabase` — на чистой базе падали, на грязной
 * зависели от того, что там лежит.
 */
final class AppTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    private function reader(): User
    {
        return User::factory()->create(['role' => User::ROLE_READER]);
    }

    public function test_the_application_admin_returns_a_successful_response(): void
    {
        $response = $this->actingAs($this->admin())->get(route('admin.main', ['lang' => 'ru']));

        $response->assertStatus(200);
    }

    public function test_the_application_admin_returns_a_failed_response(): void
    {
        $response = $this->actingAs($this->reader())->get(route('admin.main', ['lang' => 'ru']));

        $response->assertStatus(403);
    }

    public function test_the_admin_audit_index_returns_a_successful_response(): void
    {
        $response = $this->actingAs($this->admin())->get(route('audits.index'));

        $response->assertStatus(200);
    }

    public function test_the_admin_audit_create_returns_a_successful_response(): void
    {
        $response = $this->actingAs($this->admin())->get(route('audits.create'));

        $response->assertStatus(200);
    }

    public function test_the_admin_audit_store_returns_a_successful_response(): void
    {
        $user = $this->admin();
        $reports = Report::factory()->count(2)->create();

        $response = $this->actingAs($user)->post(route('audits.store'), [
            'title'     => 'Тестовый аудит',
            'user_id'   => $user->id,
            'report_id' => $reports->pluck('id')->toArray(),
        ]);

        $response->assertRedirect(route('audits.index'));
    }

    public function test_the_admin_audit_edit_returns_a_successful_response(): void
    {
        $audit = Audit::factory()->create();

        $response = $this->actingAs($this->admin())->get(route('audits.edit', $audit->id));

        $response->assertStatus(200);
    }

    public function test_the_admin_audit_show_returns_a_successful_response(): void
    {
        $audit = Audit::factory()->create();

        $response = $this->actingAs($this->admin())->get(route('audits.show', $audit->id));

        $response->assertStatus(200);
    }

    public function test_the_admin_audit_destroy_returns_a_successful_response(): void
    {
        $audit = Audit::factory()->create();

        $response = $this->actingAs($this->admin())->delete(route('audits.destroy', $audit->id));

        $response->assertRedirect(route('audits.index'));
    }

    public function test_the_admin_report_index_returns_a_successful_response(): void
    {
        $response = $this->actingAs($this->admin())->get(route('reports.index'));

        $response->assertStatus(200);
    }

    public function test_the_admin_report_create_returns_a_successful_response(): void
    {
        $response = $this->actingAs($this->admin())->get(route('reports.create'));

        $response->assertStatus(200);
    }

    public function test_the_admin_report_store_returns_a_successful_response(): void
    {
        Queue::fake();

        $response = $this->actingAs($this->admin())->post(route('reports.store'), [
            'status'     => 'Создан',
            'utility_id' => Utility::factory()->create()->id,
            'project_id' => Project::factory()->create()->id,
        ]);

        $response->assertRedirect(route('reports.index'));
        Queue::assertPushed(BotReportJob::class);
    }

    public function test_the_admin_project_store_returns_a_successful_response(): void
    {
        $data = Project::factory()->make()->toArray();

        $response = $this->actingAs($this->admin())->post(route('projects.store'), $data);

        $response->assertRedirect(route('projects.index'));
    }

    public function test_the_admin_utility_store_returns_a_successful_response(): void
    {
        $data = Utility::factory()->make()->toArray();

        $response = $this->actingAs($this->admin())->post(route('utilities.store'), $data);

        $response->assertRedirect(route('utilities.index'));
    }

    public function test_the_admin_utility_update_returns_a_successful_response(): void
    {
        $utility = Utility::factory()->create();
        $data = Utility::factory()->make()->toArray();

        $response = $this->actingAs($this->admin())->put(route('utilities.update', $utility->id), $data);

        $response->assertRedirect(route('utilities.show', $utility->id));
    }

    public function test_the_admin_utility_destroy_returns_a_successful_response(): void
    {
        $utility = Utility::factory()->create();

        $response = $this->actingAs($this->admin())->delete(route('utilities.destroy', $utility->id));

        $response->assertRedirect(route('utilities.index'));
    }

    public function test_the_admin_user_index_returns_a_successful_response(): void
    {
        $response = $this->actingAs($this->admin())->get(route('users.index', ['lang' => 'ru']));

        $response->assertStatus(200);
    }

    public function test_the_admin_user_create_returns_a_successful_response(): void
    {
        $response = $this->actingAs($this->admin())->get(route('users.create', ['lang' => 'ru']));

        $response->assertStatus(200);
    }

    /**
     * Регрессия на D27: контроллер отвечает `redirect()->route('users.index')`, а роут объявлен
     * с префиксом `{lang}/admin`. Без `URL::defaults` в `AuthUserSetLang` это UrlGenerationException
     * и 500 на создании пользователя.
     */
    public function test_the_admin_user_store_returns_a_successful_response(): void
    {
        $response = $this->actingAs($this->admin())->post(route('users.store', ['lang' => 'ru']), [
            'name'     => 'Тестовый пользователь',
            'email'    => 'test-user@example.test',
            'password' => 'secret-password',
        ]);

        $response->assertRedirect(route('users.index', ['lang' => 'ru']));
        $this->assertDatabaseHas('users', ['email' => 'test-user@example.test']);
    }
}
