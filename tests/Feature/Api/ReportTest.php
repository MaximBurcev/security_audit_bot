<?php

namespace Tests\Feature\Api;

use App\Enums\ReportStatusEnum;
use App\Models\Project;
use App\Models\Report;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;


/**
 * @group api
 */
class ReportTest extends TestCase
{

    public function test_get_all_reports_with_no_token(): void
    {
        $url = route('api.v1.reports.index');
        $response = $this->getJson($url);

        $response->assertStatus(401);
    }

    public function test_get_all_reports_with_token(): void
    {
        $this->actingAs(User::all()->random(), 'api');
        $url = route('api.v1.reports.index', ['token' => $this->generateToken()]);
        $response = $this->getJson($url);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'status',
                    'utility_id',
                    'project_id'
                ]
            ]
        ]);
    }

    public function test_get_report_with_token(): void
    {
        $this->actingAs(User::all()->random(), 'api');
        $url = route('api.v1.reports.show', ['report' => Report::all()->random(), 'token' => $this->generateToken()]);
        $response = $this->getJson($url);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'status',
                'utility_id',
                'project_id'
            ]
        ]);
    }

    public function test_create_report_with_token(): void
    {
        $this->actingAs(User::all()->random(), 'api');
        $url = route('api.v1.reports.store', ['token' => $this->generateToken()]);
        $data = [
            'status'     => ReportStatusEnum::Created,
            'utility_id' => Utility::all()->random()->id,
            'project_id' => Project::all()->random()->id
        ];
        $response = $this->postJson($url, $data);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'status',
                'utility_id',
                'project_id'
            ]
        ]);

        $newReportId = $response->json('data.id');
        $this->assertDatabaseHas('reports', ['id' => $newReportId]);
    }

    public function test_delete_report_with_no_token(): void
    {
        $url = route('api.v1.reports.destroy', ['report' => Report::all()->random()]);
        $response = $this->deleteJson($url);

        $response->assertStatus(401);
    }

    public function test_delete_report_with_token(): void
    {
        $this->actingAs(User::all()->random(), 'api');
        $url = route('api.v1.reports.destroy',
            ['report' => Report::all()->random(), 'token' => $this->generateToken()]);
        $response = $this->deleteJson($url);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);
    }

    private function generateToken()
    {
        $token = Str::random(60);
        DB::table('api_clients')->insert([
            'api_token' => hash('sha256', $token),
        ]);

        return $token;
    }
}
