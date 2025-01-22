<?php

namespace App\Event;

use App\Entity\World\Camp;
use App\ObjectDefinition\BaseDefinitionInterface;
use App\ObjectDefinition\Building\BuildingDefinitionInterface;

class ObjectTimeEvent extends GameEvent
{
    public function __construct(
        private readonly Camp                    $camp,
        private readonly BaseDefinitionInterface $definition,
        private readonly int                     $level,
        private int $time

    )
    {
    }

    public function getCamp(): Camp
    {
        return $this->camp;
    }


    public function getLevel(): int
    {
        return $this->level;
    }


    public function getTime(): int
    {
        return $this->time;
    }

    public function setTime(int $time): ObjectTimeEvent
    {
        $this->time = $time;
        return $this;
    }

    public function getDefinition(): BaseDefinitionInterface
    {
        return $this->definition;
    }



}