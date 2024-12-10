<?php

namespace App\Repositories;

use App\Models\Utility;
use App\Repositories\Contract\BaseContract;
use Illuminate\Database\Eloquent\Collection;

final class UtilityRepository extends BaseRepository implements BaseContract
{
    public function __construct(
        protected Utility $utility
    )
    {
        parent::__construct($this->utility);
    }

    public function getPublished(): Collection
    {
        return $this->model->where('is_published', true)->get();
    }
}
