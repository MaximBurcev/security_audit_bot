<?php

namespace App\Service;

use App\Http\Requests\Admin\Audit\StoreFormRequest;
use App\Http\Requests\Admin\Audit\UpdateFormRequest;
use App\Models\Audit;
use Illuminate\Foundation\Http\FormRequest;

class AuditService
{

    public function store(StoreFormRequest $request): void
    {
        $data = $request->validated();
        $reportIds = $data['report_id'];
        unset($data['report_id']);
        $audit = Audit::firstOrCreate($data);
        if (!empty($reportIds)) {
            $audit->reports()->attach($reportIds);
        }
    }

    public function update(UpdateFormRequest $request, Audit $audit): void
    {
        $data = $request->validated();
        $reportIds = $data['report_id'];
        unset($data['report_id']);
        $audit->update($data);
        $audit->reports()->sync($reportIds);
    }
}
