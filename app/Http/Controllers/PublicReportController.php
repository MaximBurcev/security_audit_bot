<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\ReportAnalyzer\NmapReportAnalyzerStrategy;
use App\ReportAnalyzer\ReportAnalyzer;
use App\ReportAnalyzer\SslReportAnalyzerStrategy;
use App\Services\ReportService;
use Illuminate\Http\Request;

class PublicReportController extends Controller
{

    public function show($reportId, Request $request, ReportService $reportService)
    {
        if ($request->hasValidSignature()) {
            $report = $reportService->get($reportId);

            $strategy = match ($report->utility->title) {
                'nikto' => $this->analyzeNiktoOutput($outputLines),
                'nmap' => new NmapReportAnalyzerStrategy(),
                'sslscan' => new SslReportAnalyzerStrategy(),
                default => [],
            };

            $recommendations = (new ReportAnalyzer($strategy))->get(explode("\n", $report->content));

            //dd($recommendations);


            return view('public_report', compact('report', 'recommendations'));
        } else {
            abort(401);
        }

    }
}
