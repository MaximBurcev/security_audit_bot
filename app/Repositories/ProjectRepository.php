<?php

namespace App\Repositories;

use App\Models\Project;
use App\Repositories\Contract\BaseContract;
use Illuminate\Database\Eloquent\Collection;

final class ProjectRepository extends BaseRepository implements BaseContract
{
    public function __construct(
        protected Project $project
    )
    {
        parent::__construct($this->project);
    }

    public function getPublished(): Collection
    {
        return $this->model->where('is_published', true)->get();
    }
}
