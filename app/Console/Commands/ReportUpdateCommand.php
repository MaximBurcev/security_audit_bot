<?php

namespace App\Console\Commands;

use App\Enums\ReportStatusEnum;
use App\Models\Project;
use App\Models\Report;
use App\Models\User;
use App\Models\Utility;
use App\Notifications\ReportUpdate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class ReportUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:report.update
                            {report : Report id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update report';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reportId = $this->argument('report');
        $report = Report::where('id', '=', $reportId)->get()->first();
        $report->update(['status' => ReportStatusEnum::InProcess]);
        $utility = Utility::query()->findOrFail($report->utility_id);
        $project = Project::query()->findOrFail($report->project_id);
        $command = $utility->command;
        $url = $project->url;
        $content = shell_exec(implode(" ", [$command, parse_url($url, PHP_URL_HOST)]));
        $report->update([
            'content' => $content,
            'status'  => ReportStatusEnum::Finished
        ]);
        $reportUrl = URL::signedRoute('public-report',
            ['report' => $report->id]);
        Log::channel('slackReport')->debug('Отчет обновился: ' . $reportUrl);
        info('report.update', [$reportUrl]);
        foreach ($report->audits as $audit) {
            $audit->user->notify(new ReportUpdate($reportUrl));
        }
    }
}
