<?php

namespace App\Modules\Core;

use App\Entity\World\Fleet;
use App\Entity\World\PlanetShip;
use App\Entity\World\Queue\Queue;
use App\Modules\Construction\Service\ConstructionService;
use App\Modules\Core\Entity\Planet;
use App\Object\ResourcePack;
use App\ObjectRegistry\BuildingRegistry;
use App\Repository\FleetRepository;
use App\Repository\ShipRepository;
use App\Service\ResearchService;
use App\Service\ResourceService;
use App\Service\StorageService;

readonly class PlanetFacade
{
    public function __construct(
        private BuildingRegistry    $buildingConfigurationService,
        private ConstructionService $constructionService,
        private StorageService      $storageService,
        private ResourceService     $resourceService,
        private ShipRepository      $shipRepository,
        private FleetRepository     $fleetRepository, private ResearchService $researchService,
    )
    {}

    public function getCostForBuilding(Planet $planet, string $buildingName, ?int $level = null): ResourcePack
    {
        return $this->constructionService->getCostForBuilding($planet, $buildingName, $level);
    }

    public function canBeBuilt(Planet $planet, string $buildingName, ?int $level = null, ?ResourcePack $cost = null): bool
    {
        return $this->constructionService->canBeBuilt($planet, $buildingName, $level, $cost);
    }

    /**
     * @return int The time in seconds to build the building at the given or latest level
     */
    public function getBuildTime(Planet $planet, string $buildingName, ?int $level = null): int
    {
        return $this->constructionService->getBuildTime($planet, $buildingName, $level);
    }

    public function getConstructionQueue(Planet $planet): Queue
    {
        return $this->constructionService->getConstructionQueue($planet);
    }

    public function getBuildingRequirements(string $buildingName): array
    {
        $config = $this->buildingConfigurationService->get($buildingName);

        return $config->getRequirements()->getRequiredBuildings();
    }

    public function getHourlyProduction(Planet $planet): ResourcePack
    {
        return $this->resourceService->getHourlyProduction($planet);
    }

    /**
     * @return array<Fleet>
     */
    public function getFleets(Planet $planet): array
    {
        return $this->fleetRepository->getFleetsByPlanet($planet);
    }

    /** @return array<PlanetShip> */
    public function getUngroupedShips(Planet $planet): array
    {
        return $this->shipRepository->getUngroupedShipsByPlanet($planet);
    }

    public function costForResearch(Planet $planet, string $techName, int $level): ResourcePack
    {
        return $this->researchService->getCostForResearch($planet, $techName, $level);
    }
}