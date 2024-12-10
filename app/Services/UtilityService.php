<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Utility;
use App\Repositories\UtilityRepository;
use Illuminate\Database\Eloquent\Collection;

final class UtilityService
{
    public function __construct(
        protected UtilityRepository $utilityRepository
    )
    {
    }

    public function get(int $id): Utility
    {
        return $this->utilityRepository->find($id);
    }

    public function getAll(): Collection
    {
        return $this->utilityRepository->findAll();
    }

    public function create(array $data = []): Utility
    {
        return $this->utilityRepository->create($data);
    }

    public function update(int $id, array $data = []): Utility
    {
        return $this->utilityRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->utilityRepository->delete($id);
    }
}
