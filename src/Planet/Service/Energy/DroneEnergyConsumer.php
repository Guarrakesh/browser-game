<?php

namespace App\Planet\Service\Energy;

use App\Energy\Domain\EnergyConsumerInterface;
use App\Planet\Domain\Entity\Planet;
use App\Planet\Service\DroneService;

readonly class DroneEnergyConsumer implements EnergyConsumerInterface
{
    public function __construct(
        private DroneService $droneService,

    )
    {
    }

    public function getEnergyConsumption(Planet $planet): float
    {
        $numDrones = $this->droneService->getTotalDroneCount($planet);

        return $this->droneService->getDroneEnergyConsumption() * $numDrones;
    }
}