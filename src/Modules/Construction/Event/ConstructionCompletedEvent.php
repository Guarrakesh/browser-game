<?php

namespace App\Modules\Construction\Event;

use App\Event\GameEvent;
use App\Modules\Planet\Model\Entity\PlanetConstruction;

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