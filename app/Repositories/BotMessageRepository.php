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

    public function getPublished(): Collection
    {
        return $this->model->where('is_published', true)->get();
    }
}
