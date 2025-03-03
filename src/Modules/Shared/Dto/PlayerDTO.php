<?php

namespace App\Modules\Shared\Dto;

readonly class PlayerDTO
{
    public function __construct(public string $id, public string $userId, public \DateTimeImmutable $joinedAt)
    {

    }
}