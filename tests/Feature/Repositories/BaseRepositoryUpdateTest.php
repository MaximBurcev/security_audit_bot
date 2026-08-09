<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories;

use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Регрессия на рассогласование контракта: `BaseRepository::update()` возвращал `int`
 * (число затронутых строк), а сервисы объявлены возвращающими модель — при `strict_types`
 * любой вызов падал с TypeError. Боевой путь — `ProjectSyncService::sync()`.
 */
final class BaseRepositoryUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_returns_updated_model(): void
    {
        $project = Project::factory()->create(['title' => 'Было']);

        $updated = app(ProjectService::class)->update($project->id, ['title' => 'Стало']);

        $this->assertInstanceOf(Project::class, $updated);
        $this->assertSame($project->id, $updated->id);
        $this->assertSame('Стало', $updated->title);
    }

    public function test_update_persists_changes(): void
    {
        $project = Project::factory()->create(['title' => 'Было']);

        app(ProjectService::class)->update($project->id, ['title' => 'Стало']);

        $this->assertDatabaseHas('projects', [
            'id'    => $project->id,
            'title' => 'Стало',
        ]);
    }

    public function test_update_throws_on_missing_id(): void
    {
        $this->expectException(ModelNotFoundException::class);

        app(ProjectService::class)->update(404, ['title' => 'Стало']);
    }
}
