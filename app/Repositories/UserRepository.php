<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contract\BaseContract;
use Illuminate\Database\Eloquent\Collection;

final class UserRepository extends BaseRepository implements BaseContract
{
    public function __construct(
        protected User $user
    )
    {
        parent::__construct($this->user);
    }

    public function getPublished(): Collection
    {
        return $this->model->where('is_published', true)->get();
    }

    public function getByTelegramId(?int $userId)
    {
        return $this->model->where('telegram_user_id', '=', $userId)->get()->first();
    }
}
