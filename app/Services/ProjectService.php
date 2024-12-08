<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Project;
use App\Repositories\ProjectRepository;
use Illuminate\Database\Eloquent\Collection;

final class ProjectService
{
    public function __construct(
        protected ProjectRepository $projectRepository
    )
    {
    }

    public function get(int $id): Project
    {
        return $this->projectRepository->find($id);
    }

    public function getAll(): Collection
    {
        return $this->projectRepository->findAll();
    }

    public function create(array $data = []): Project
    {
        return $this->projectRepository->create($data);
    }

    public function update(int $id, array $data = []): Project
    {
        return $this->projectRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->projectRepository->delete($id);
    }
}
