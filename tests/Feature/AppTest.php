<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Audit;
use Tests\TestCase;

class AppTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/admin');

        $response->assertStatus(200);
    }

    public function test_the_admin_audit_index_returns_a_successful_response(): void
    {
        $response = $this->get(route('audits.index'));

        $response->assertStatus(200);
    }

    public function test_the_admin_audit_create_returns_a_successful_response(): void
    {
        $response = $this->get(route('audits.create'));

        $response->assertStatus(200);
    }

    public function test_the_admin_audit_show_returns_a_successful_response(): void
    {
        $audit = Audit::all()->first();
        $url = route('audits.show', $audit->id);
        $response = $this->get($url);

        $response->assertStatus(200);
    }

    public function test_the_admin_audit_store_returns_a_successful_response(): void
    {


        $url = route('audits.store');
        $response = $this->post($url);

        $response->assertStatus(302);
    }
}
