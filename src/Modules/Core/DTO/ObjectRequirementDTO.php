<?php

namespace App\Modules\Core\DTO;

class ObjectRequirementDTO
{
    public function __construct(protected readonly string $name, protected readonly string $type, protected readonly int $level) {}


    public function getName(): string
    {
        return $this->name;
    }

    public function getLevel(): int
    {
        return $this->level;
    }



}