<?php

namespace App\Service;

use App\Http\Requests\Admin\Audit\StoreRequest;
use App\Http\Requests\Admin\Audit\UpdateRequest;
use App\Models\Audit;

class AuditService
{

    public function store(StoreRequest $request): void
    {
        $data = $request->validated();
        $reportIds = $data['report_id'];
        unset($data['report_id']);
        $audit = Audit::firstOrCreate($data);
        if (!empty($reportIds)) {
            $audit->reports()->attach($reportIds);
        }
    }

    public function update(UpdateRequest $request, Audit $audit): void
    {
        $data = $request->validated();
        $reportIds = $data['report_id'];
        unset($data['report_id']);
        $audit->update($data);
        $audit->reports()->sync($reportIds);
    }
}
