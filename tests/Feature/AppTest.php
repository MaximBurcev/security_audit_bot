<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Audit;
use App\Models\Project;
use App\Models\Report;
use App\Models\User;
use App\Models\Utility;
use Tests\TestCase;

class AppTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_admin_returns_a_successful_response(): void
    {

        $user = User::where('role', User::ROLE_ADMIN)->first();

        $response = $this->actingAs($user)->get(route('admin.main'));

        $response->assertStatus(200);
    }

    public function test_the_application_admin_returns_a_failed_response(): void
    {

        $user = User::where('role', User::ROLE_READER)->first();

        $response = $this->actingAs($user)->get(route('admin.main'));

        $response->assertStatus(403);
    }

    public function test_the_admin_audit_index_returns_a_successful_response(): void
    {
        $user = User::where('role', User::ROLE_ADMIN)->first();

        $response = $this->actingAs($user)->get(route('audits.index'));

        $response->assertStatus(200);
    }

    public function test_the_admin_audit_create_returns_a_successful_response(): void
    {
        $user = User::where('role', User::ROLE_ADMIN)->first();

        $response = $this->actingAs($user)->get(route('audits.create'));

        $response->assertStatus(200);
    }

    public function test_the_admin_audit_store_returns_a_successful_response(): void
    {
        $user = User::where('role', User::ROLE_ADMIN)->first();
        $audit = Audit::factory()->make()->toArray();
        $audit['report_id'] = Report::all()->random(2)->pluck('id')->toArray();
        $response = $this->actingAs($user)->post(route('audits.store', $audit));

        $response->assertRedirect(route('audits.index'));
    }

    public function test_the_admin_audit_edit_returns_a_successful_response(): void
    {
        $user = User::where('role', User::ROLE_ADMIN)->first();
        $audit = Audit::all()->first();
        $response = $this->actingAs($user)->get(route('audits.edit', $audit->id));

        $response->assertStatus(200);
    }

    public function test_the_admin_audit_show_returns_a_successful_response(): void
    {
        $user = User::where('role', User::ROLE_ADMIN)->first();
        $audit = Audit::all()->first();
        $url = route('audits.show', $audit->id);
        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(200);
    }

    public function test_the_admin_audit_destroy_returns_a_successful_response(): void
    {
        $user = User::where('role', User::ROLE_ADMIN)->first();
        $audit = Audit::all()->random();
        $url = route('audits.destroy', $audit->id);
        $response = $this->actingAs($user)->delete($url);
        $response->assertRedirect(route('audits.index'));
    }



    public function test_the_admin_report_index_returns_a_successful_response(): void
    {
        $user = User::where('role', User::ROLE_ADMIN)->first();

        $response = $this->actingAs($user)->get(route('reports.index'));

        $response->assertStatus(200);
    }

    public function test_the_admin_report_create_returns_a_successful_response(): void
    {
        $user = User::where('role', User::ROLE_ADMIN)->first();

        $response = $this->actingAs($user)->get(route('reports.create'));

        $response->assertStatus(200);
    }

    public function test_the_admin_report_store_returns_a_successful_response(): void
    {
        $user = User::where('role', User::ROLE_ADMIN)->first();

        $response = $this->actingAs($user)->post(route('reports.store'), Report::factory()->make()->toArray());

        $response->assertRedirect(route('reports.index'));
    }

    public function test_the_admin_project_store_returns_a_successful_response(): void
    {
        $user = User::where('role', User::ROLE_ADMIN)->first();
        $url = route('projects.store');
        $data = Project::factory()->make()->toArray();
        $response = $this->actingAs($user)->post($url, $data);
        $response->assertRedirect(route('projects.index'));
    }

    public function test_the_admin_utility_store_returns_a_successful_response(): void
    {
        $user = User::where('role', User::ROLE_ADMIN)->first();
        $url = route('utilities.store');
        $data = Utility::factory()->make()->toArray();
        $response = $this->actingAs($user)->post($url, $data);
        $response->assertRedirect(route('utilities.index'));
    }

    public function test_the_admin_utility_update_returns_a_successful_response(): void
    {
        $user = User::where('role', User::ROLE_ADMIN)->first();
        $utility = Utility::all()->random();
        $url = route('utilities.update', $utility->id);
        $response = $this->actingAs($user)->put($url, Utility::factory()->make()->toArray());
        $response->assertRedirect(route('utilities.show', $utility->id));
    }

    public function test_the_admin_utility_destroy_returns_a_successful_response(): void
    {
        $user = User::where('role', User::ROLE_ADMIN)->first();
        $utility = Utility::all()->random();
        $url = route('utilities.destroy', $utility->id);
        $response = $this->actingAs($user)->delete($url, $utility->toArray());
        $response->assertRedirect(route('utilities.index'));
    }

    public function test_the_admin_user_index_returns_a_successful_response(): void
    {
        $user = User::where('role', User::ROLE_ADMIN)->first();
        $url = route('users.index');
        $response = $this->actingAs($user)->get($url);
        $response->assertStatus(200);
    }

    public function test_the_admin_user_create_returns_a_successful_response(): void
    {
        $user = User::where('role', User::ROLE_ADMIN)->first();
        $url = route('users.create');
        $response = $this->actingAs($user)->get($url);
        $response->assertStatus(200);
    }

    public function test_the_admin_user_store_returns_a_successful_response(): void
    {
        $user = User::where('role', User::ROLE_ADMIN)->first();
        $url = route('users.store');
        $arUser = User::factory()->make()->toArray();
        $arUser['password'] = md5(fake()->password());
        //dd($arUser);
        $response = $this->actingAs($user)->post($url, $arUser);
        $response->assertRedirect(route('users.index'));
    }
}
