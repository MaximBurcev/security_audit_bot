<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class PublicReportController extends Controller
{

    public function show($reportId, Request $request)
    {
        if ($request->hasValidSignature()) {
            $report = Report::findOrFail($reportId);

            return view('public_report', compact('report'));
        } else {
            abort(401);
        }

    }
}
