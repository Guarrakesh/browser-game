<?php

namespace App\Model;

class ObjectRequirement
{
    public function __construct(protected readonly string $objectName, protected readonly int $level) {}


    public function getObjectName(): string
    {
        return $this->objectName;
    }

    public function getLevel(): int
    {
        return $this->level;
    }


}