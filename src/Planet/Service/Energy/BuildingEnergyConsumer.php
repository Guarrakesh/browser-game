<?php

namespace App\Planet\Service\Energy;

use App\Energy\Domain\EnergyConsumerInterface;
use App\Planet\Application\Service\PlanetService;
use App\Planet\Domain\Entity\Planet;

readonly class BuildingEnergyConsumer implements EnergyConsumerInterface
{

    public function __construct(
        private PlanetService $planetService
    )
    {
    }

    public function getEnergyConsumption(Planet $planet): float
    {
        $buildings = $planet->getBuildingsAsGameObjects();
        $queue = $planet->getQueuedJobs();

        $energy = 0;
        foreach ($buildings as $building) {
            $energy += $building->getEnergyConsumption();
        }

        foreach ($queue as $job) {
            $energy += $job->getDefinition()->getEnergyIncreaseAtLevel($job->getLevel());
        }

        return $energy;
    }
}