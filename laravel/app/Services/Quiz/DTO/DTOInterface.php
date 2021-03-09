<?php


namespace App\Services\Quiz\DTO;


interface DTOInterface
{
    public static function fromArray(array $data): self;
    public function toArray(): array;
}
