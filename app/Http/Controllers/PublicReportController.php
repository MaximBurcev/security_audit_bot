<?php

namespace App\Http\Controllers;

use App\ReportAnalyzer\NiktoReportAnalyzerStrategy;
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

            $data = json_decode($report->content, true);

            if (is_array($data) && array_key_exists('raw', $data)) {
                // Новый формат: JSON с предвычисленным анализом
                $raw = $data['raw'] ?? '';
                $recommendations = $data['analysis'] ?? [];
            } else {
                // Старый формат: сырой текст — анализируем на лету
                $raw = $report->content;
                $strategy = match ($report->utility->title) {
                    'nikto'   => new NiktoReportAnalyzerStrategy(),
                    'nmap'    => new NmapReportAnalyzerStrategy(),
                    'sslscan' => new SslReportAnalyzerStrategy(),
                    default   => null,
                };
                $recommendations = $strategy
                    ? (new ReportAnalyzer($strategy))->get(explode("\n", $raw))
                    : [];
            }

            return view('public_report', compact('report', 'raw', 'recommendations'));
        } else {
            abort(401);
        }
    }
}
