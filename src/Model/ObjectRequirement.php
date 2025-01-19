<?php

namespace App\Model;

class ObjectRequirement
{
    public function __construct(protected readonly string $name, protected readonly int $level) {}


    public function getName(): string
    {
        return $this->name;
    }

    public function getLevel(): int
    {
        return $this->level;
    }



}