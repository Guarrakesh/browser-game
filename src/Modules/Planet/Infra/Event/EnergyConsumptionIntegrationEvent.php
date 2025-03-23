<?php

namespace App\Modules\Planet\Infra\Event;

use App\Event\GameEvent;

class EnergyConsumptionIntegrationEvent extends GameEvent
{
    public function __construct(private float $energy, private readonly int $planetId)
    {
    }

    public function getPlanetId(): int
    {
        return $this->planetId;
    }


    public function getEnergy(): float
    {
        return $this->energy;
    }

    public function setEnergy(float $energy): EnergyConsumptionIntegrationEvent
    {
        $this->energy = $energy;
        return $this;
    }


}