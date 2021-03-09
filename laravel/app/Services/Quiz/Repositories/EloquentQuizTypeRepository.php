<?php


namespace App\Services\Quiz\Repositories;

use App\Models\QuizType;
use App\Services\Quiz\DTO\DTOInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentQuizTypeRepository
{
    const DEFAULT_PERPAGE = 10;

    public function search(?int $perPage = null, array $with= []): LengthAwarePaginator
    {
        $query = QuizType::query();
        $query->with($with);
        $query->orderByDesc('id');
        return $query->paginate( $perPage ?? static::DEFAULT_PERPAGE );
    }

    public function createFromDTO(DTOInterface $dto): QuizType
    {
        $data = $dto->toArray();
        return QuizType::create($data);
    }

}
