<?php

namespace App\Camp;

use App\Entity\World\Camp;
use App\Entity\World\CampShip;
use App\Entity\World\Fleet;
use App\Entity\World\Queue\Queue;
use App\Object\ResourcePack;
use App\ObjectRegistry\BuildingRegistry;
use App\Repository\FleetRepository;
use App\Repository\ShipRepository;
use App\Service\ConstructionService;
use App\Service\ResourceService;
use App\Service\StorageService;

readonly class CampFacade
{
    public function __construct(
        private BuildingRegistry    $buildingConfigurationService,
        private ConstructionService $constructionService,
        private StorageService      $storageService,
        private ResourceService     $resourceService,
        private ShipRepository      $shipRepository,
        private FleetRepository     $fleetRepository,
    )
    {}

    public function getMaxStorage(Camp $camp): int
    {
        return $this->storageService->getMaxStorage($camp);
    }

    /**
     * TODO: find a way to cache these calculations, using a cache adapter and memoization.
     */
    public function getCostForBuilding(Camp $camp, string $buildingName, ?int $level = null): ResourcePack
    {
        return $this->constructionService->getCostForBuilding($camp, $buildingName, $level);
    }

    public function canBeBuilt(Camp $camp, string $buildingName, ?int $level = null, ?ResourcePack $cost = null): bool
    {
        return $this->constructionService->canBeBuilt($camp, $buildingName, $level, $cost);
    }

    /**
     * @return int The time in seconds to build the building at the given or latest level
     */
    public function getBuildTime(Camp $camp, string $buildingName, ?int $level = null): int
    {
        return $this->constructionService->getBuildTime($camp, $buildingName, $level);
    }

    public function getConstructionQueue(Camp $camp): Queue
    {
        return $this->constructionService->getCampQueue($camp);
    }

    public function getBuildingRequirements(string $buildingName): array
    {
        $config = $this->buildingConfigurationService->getBuildingConfigProvider($buildingName);

        return $config->getRequirements()->getRequiredBuildings();
    }

    public function getHourlyProduction(Camp $camp): ResourcePack
    {
        return $this->resourceService->getHourlyProduction($camp);
    }

    /**
     * @return array<Fleet>
     */
    public function getFleets(Camp $camp): array
    {
        return $this->fleetRepository->getFleetsByCamp($camp);
    }

    /** @return array<CampShip> */
    public function getUngroupedShips(Camp $camp): array
    {
        return $this->shipRepository->getUngroupedShipsByCamp($camp);
    }
}