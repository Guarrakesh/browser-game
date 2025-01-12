<?php

namespace App\Event\Construction;

use App\Entity\World\Queue\CampConstruction;
use App\Event\GameEvent;

class ConstructionCompletedEvent extends GameEvent
{
    public function __construct(private readonly CampConstruction $construction)
    {
    }

    public function getConstruction(): CampConstruction
    {
        return $this->construction;
    }


}