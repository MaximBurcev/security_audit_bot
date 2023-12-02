<?php

declare(strict_types=1);

namespace App\Models\Events;

use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReportCreated
{
    public function __construct(Report $report)
    {
        Log::channel('slackReport')->info('Отчет создан');
        //Mail::mailer('sendmail')->to($report->user)->send(new \App\Mail\ReportCreated());
    }
}
