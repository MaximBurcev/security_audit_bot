<?php

namespace App\Services;

use App\Http\Requests\Admin\Audit\StoreFormRequest;
use App\Http\Requests\Admin\Audit\UpdateFormRequest;
use App\Models\Audit;
use App\Repositories\AuditRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;

class AuditService
{
    public function __construct(
        protected AuditRepository $auditRepository,
    )
    {
    }

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

    public function updateByRequest(UpdateFormRequest $request, Audit $audit): void
    {
        $data = $request->validated();
        $reportIds = $data['report_id'];
        unset($data['report_id']);
        $audit->update($data);
        $audit->reports()->sync($reportIds);
    }

    public function get(int $id): Audit
    {
        return $this->auditRepository->find($id);
    }

    public function getAll(): Collection
    {
        return $this->auditRepository->findAll();
    }

    public function create(array $data = []): Audit
    {
        return $this->auditRepository->create($data);
    }

    public function update(int $id, array $data = []): Audit
    {
        return $this->auditRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->auditRepository->delete($id);
    }

    public function getCount(): int
    {
        return AuditRepository::count() + 1;
    }
}
