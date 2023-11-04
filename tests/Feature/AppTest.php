<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Audit;
use App\Models\Project;
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

    public function test_the_admin_audit_show_returns_a_successful_response(): void
    {
        $user = User::where('role', User::ROLE_ADMIN)->first();
        $audit = Audit::all()->first();
        $url = route('audits.show', $audit->id);
        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(200);
    }

    public function test_the_admin_audit_store_returns_a_successful_response(): void
    {
        $user = User::where('role', User::ROLE_ADMIN)->first();
        $url = route('audits.store');
        $data = Audit::factory()->make()->toArray();
        $response = $this->actingAs($user)->post($url, $data);
        //$response->assertRedirect(route('audits.index'));
        $response->assertStatus(302);
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
}
