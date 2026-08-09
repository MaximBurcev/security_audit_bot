<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Models\Report;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Расписание строится из таблицы `tasks`, а cron-выражение оттуда приходит из формы админки.
 * Невалидное выражение разбирается уже внутри `schedule:run` и роняет весь прогон — вместе с
 * задачами, к битой записи отношения не имеющими. Проверяем оба рубежа: валидацию на входе и
 * устойчивость планировщика к записи, которая проскочила раньше.
 */
final class ScheduleTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    public function test_task_with_invalid_cron_is_rejected(): void
    {
        $response = $this->actingAs($this->admin())->post(route('tasks.store'), [
            'title'       => 'Битая задача',
            'report_id'   => Report::factory()->create()->id,
            'cron_format' => '99 * * * *',
        ]);

        $response->assertSessionHasErrors('cron_format');
        $this->assertDatabaseMissing('tasks', ['title' => 'Битая задача']);
    }

    public function test_task_with_valid_cron_is_accepted(): void
    {
        $response = $this->actingAs($this->admin())->post(route('tasks.store'), [
            'title'       => 'Ночной аудит',
            'report_id'   => Report::factory()->create()->id,
            'cron_format' => '0 3 * * *',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', ['title' => 'Ночной аудит', 'cron_format' => '0 3 * * *']);
    }

    public function test_broken_task_row_does_not_break_the_schedule(): void
    {
        // Запись, заведённая до появления валидации: в БД лежит мусор.
        Task::create([
            'title'       => 'Битая задача',
            'report_id'   => Report::factory()->create()->id,
            'cron_format' => 'каждую пятницу',
        ]);

        Task::create([
            'title'       => 'Ночной аудит',
            'report_id'   => Report::factory()->create()->id,
            'cron_format' => '0 3 * * *',
        ]);

        $this->artisan('schedule:list')
            ->expectsOutputToContain('app:report.update')
            ->assertSuccessful();
    }
}
