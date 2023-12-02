<?php

declare(strict_types=1);

namespace App\Models\Events;

use App\Models\Report;
use Illuminate\Support\Facades\Log;

class ReportCreating
{
    public function __construct(Report $report)
    {
        Log::channel('slackReport')->info('Отчет создается');
    }
}
