<?php

namespace App\Repositories;

use App\Models\Report;
use App\Repositories\Contract\BaseContract;
use Illuminate\Database\Eloquent\Collection;

final class ReportRepository extends BaseRepository implements BaseContract
{
    public function __construct(
        protected Report $report
    )
    {
        parent::__construct($this->report);
    }

    public function getPublished(): Collection
    {
        return $this->model->where('is_published', true)->get();
    }

    public function paginate(int $perPage): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->model->whereNull('deleted_at')->paginate($perPage);
    }
}
