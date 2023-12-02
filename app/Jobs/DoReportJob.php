<?php

namespace App\Jobs;

use App\Enums\ReportStatusEnum;
use App\Models\Project;
use App\Models\Report;
use App\Models\Utility;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DoReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $data;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $utility = Utility::query()->findOrFail($this->data['utility_id']);
        $project = Project::query()->findOrFail($this->data['project_id']);
        $command = $utility->command;
        $url = $project->url;
        $this->data['content'] = shell_exec($command . " ". parse_url($url, PHP_URL_HOST));
        $this->data['status'] = ReportStatusEnum::Finished;
        Log::info(__CLASS__, [$utility->command]);
        Log::info(__CLASS__, $this->data);
        Report::firstOrCreate($this->data);
    }
}
