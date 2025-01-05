<?php

namespace App\Services;

use App\Enums\ReportStatusEnum;
use App\Http\Requests\Admin\Report\StoreFormRequest;
use App\Http\Requests\Admin\Report\UpdateFormRequest;
use App\Jobs\BotReportJob;
use App\Models\Report;
use App\Repositories\ReportRepository;
use Illuminate\Database\Eloquent\Collection;

class ReportService
{

    public function __construct(
        protected ReportRepository $reportRepository
    )
    {

    }

    public function store(StoreFormRequest $request): void
    {
        $data = $request->validated();
        BotReportJob::dispatch($data);
    }

    public function create($arAuditDataItem): \Illuminate\Database\Eloquent\Model
    {
        $arReportData = [
            'project_id' => $arAuditDataItem['projectId'],
            'utility_id' => $arAuditDataItem['utilityId'],
            'content'    => '',
            'status'     => ReportStatusEnum::Created
        ];

        return $this->reportRepository->create($arReportData);
    }

    public function get(int $id): Report
    {
        return $this->reportRepository->find($id);
    }

    public function getAll(): Collection
    {
        return $this->reportRepository->findAll();
    }


    public function update(int $id, array $data = []): int
    {
        return $this->reportRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->reportRepository->delete($id);
    }

}
