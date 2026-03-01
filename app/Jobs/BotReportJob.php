<?php

namespace App\Jobs;

use App\Enums\ReportStatusEnum;
use App\ReportAnalyzer\NiktoReportAnalyzerStrategy;
use App\ReportAnalyzer\NmapReportAnalyzerStrategy;
use App\ReportAnalyzer\ReportAnalyzer;
use App\ReportAnalyzer\ReportAnalyzerInterface;
use App\ReportAnalyzer\SslReportAnalyzerStrategy;
use App\Services\ProjectService;
use App\Services\ReportService;
use App\Services\UtilityService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BotReportJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected array $arBotReportJobData,
    )
    {
    }

    /**
     * Execute the job.
     */
    public function handle(UtilityService $utilityService, ProjectService $projectService, ReportService $reportService): void
    {
        $utility = $utilityService->get($this->arBotReportJobData['utilityId']);
        $project = $projectService->get($this->arBotReportJobData['projectId']);
        $url = $project->url;

        $reportService->update($this->arBotReportJobData['reportId'], [
            'status' => ReportStatusEnum::InProcess
        ]);

        $raw = $this->stripAnsi(shell_exec($utility->command . " " . escapeshellarg(parse_url($url, PHP_URL_HOST))) ?? '');

        Log::info(__CLASS__, ['command' => $utility->command, 'url' => $url]);

        $analysis = $this->analyze($utility->title, $raw);

        $reportService->update($this->arBotReportJobData['reportId'], [
            'content' => json_encode(['raw' => $raw, 'analysis' => $analysis]),
            'status'  => ReportStatusEnum::Finished
        ]);
    }

    private function stripAnsi(string $text): string
    {
        return preg_replace('/\x1b\[[0-9;]*[A-Za-z]/', '', $text);
    }

    private function analyze(string $utilityTitle, string $raw): array
    {
        $strategy = match ($utilityTitle) {
            'nikto'   => new NiktoReportAnalyzerStrategy(),
            'nmap'    => new NmapReportAnalyzerStrategy(),
            'sslscan' => new SslReportAnalyzerStrategy(),
            default   => null,
        };

        if ($strategy === null) {
            return [];
        }

        return (new ReportAnalyzer($strategy))->get(explode("\n", $raw));
    }
}
