<?php

namespace App\Repositories;

use App\Models\BotMessage;
use App\Repositories\Contract\BaseContract;
use Illuminate\Database\Eloquent\Collection;

final class BotMessageRepository extends BaseRepository implements BaseContract
{
    public function __construct(
        protected BotMessage $botMessage
    )
    {
        parent::__construct($this->botMessage);
    }

    public function getByUserId($userId): Collection
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function deleteByUserId(int $userId): bool
    {
        return $this->model->where('user_id', '=', $userId)->delete();
    }
}
