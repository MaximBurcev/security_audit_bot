<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class UserService
{
    public function __construct(
        protected UserRepository $userRepository
    )
    {
    }

    public function get(int $id): User
    {
        return $this->userRepository->find($id);
    }

    public function getAll(): Collection
    {
        return $this->userRepository->findAll();
    }

    public function create(array $data = []): User
    {
        return $this->userRepository->create($data);
    }

    public function update(int $id, array $data = []): User
    {
        return $this->userRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->userRepository->delete($id);
    }

    public function getByTelegramId(?int $userId)
    {
        return $this->userRepository->getByTelegramId($userId);
    }

    public function addTelegramUser(\SergiX44\Nutgram\Telegram\Types\User\User $telegramUser): void
    {
        $this->userRepository->create([
            'name'              => $telegramUser->username?? $telegramUser->first_name,
            'email'             => $telegramUser->username?? $telegramUser->first_name . '@' . parse_url(config('app.url'), PHP_URL_HOST),
            'email_verified_at' => now(),
            'password'          => Hash::make(fake()->password()),
            'remember_token'    => Str::random(10),
            'telegram_user_id'  => $telegramUser->id
        ]);
    }
}
