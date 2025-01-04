<?php

namespace App\Repositories;

use App\Models\Audit;
use App\Repositories\Contract\BaseContract;
use Illuminate\Database\Eloquent\Collection;

final class AuditRepository extends BaseRepository implements BaseContract
{
    public function __construct(
        protected Audit $audit
    )
    {
        parent::__construct($this->audit);
    }

    public static function count()
    {
        return Audit::count();
    }

    public function getPublished(): Collection
    {
        return $this->model->where('is_published', true)->get();
    }
}
