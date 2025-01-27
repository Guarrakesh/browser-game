<?php

namespace App\Event\Construction;

use App\Entity\World\Queue\PlanetConstruction;
use App\Event\GameEvent;

class ConstructionCompletedEvent extends GameEvent
{
    public function __construct(private readonly PlanetConstruction $construction)
    {
    }

    public function getConstruction(): PlanetConstruction
    {
        return $this->construction;
    }


}