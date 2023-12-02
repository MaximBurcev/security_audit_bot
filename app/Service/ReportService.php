<?php

namespace App\Service;

use App\Http\Requests\Admin\Report\StoreFormRequest;
use App\Http\Requests\Admin\Report\UpdateFormRequest;
use App\Jobs\DoReportJob;
use App\Models\Audit;
use App\Models\Utility;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class ReportService
{

    public function store(StoreFormRequest $request): void
    {
        $data = $request->validated();
        DoReportJob::dispatch($data);
    }


}
