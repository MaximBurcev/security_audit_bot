<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;

class PublicReportController extends Controller
{

    public function show($reportId, Request $request, ReportService $reportService)
    {
        if ($request->hasValidSignature()) {
            $report = $reportService->get($reportId);

            $data = json_decode($report->content, true);
            $raw = $data['raw'] ?? $report->content;
            $recommendations = $data['analysis'] ?? [];

            return view('public_report', compact('report', 'raw', 'recommendations'));
        } else {
            abort(401);
        }
    }
}
