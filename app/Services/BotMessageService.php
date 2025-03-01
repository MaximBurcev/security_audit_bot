<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BotMessage;
use App\Repositories\BotMessageRepository;
use Illuminate\Database\Eloquent\Collection;

final class BotMessageService
{
    public function __construct(
        protected BotMessageRepository $botMessageRepository, protected UserService $userService
    )
    {
    }

    public function get(int $id): BotMessage
    {
        return $this->botMessageRepository->find($id);
    }

    public function getAll(): Collection
    {
        return $this->botMessageRepository->findAll();
    }

    public function create(array $data = []): BotMessage
    {
        return $this->botMessageRepository->create($data);
    }

    public function update(int $id, array $data = []): BotMessage
    {
        return $this->botMessageRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->botMessageRepository->delete($id);
    }

    public function getByUserId(int $userId): Collection
    {
        return $this->botMessageRepository->getByUserId($userId);
    }

    public function deleteByTelegramUserId(int $telegramUserId): bool
    {
        return $this->botMessageRepository->deleteByUserId($telegramUserId);
    }

    public function deleteByUserId(?int $userId): bool
    {
        return $this->botMessageRepository->deleteByUserId($userId);
    }

}
