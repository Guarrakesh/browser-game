<?php

namespace App\Modules\Planet\Service;

use App\Exception\GameException;
use App\Modules\Core\Infra\Repository\UniverseSettingsRepository;
use App\Modules\Planet\Dto\EnergyDTO;
use App\Modules\Planet\Dto\PlanetDTO;
use App\Modules\Planet\Infra\Repository\PlanetRepository;
use App\Modules\Planet\Model\DomainService\Production\ProductionService;
use App\Modules\Shared\Dto\GameObjectLevel;
use App\Modules\Shared\Model\ResourcePack;

class PlanetService
{
    public function __construct(private readonly PlanetRepository  $planetRepository,
                                private UniverseSettingsRepository $universeSettingsRepository,
                                private ProductionService          $productionService,
                                private DroneService               $droneService, private readonly EnergyService $powerService,
    )
    {
    }

    public function getPlanet(int $planetId): PlanetDTO
    {

        $planetDto = new PlanetDTO();
        $planet = $this->planetRepository->find($planetId);

        $planetDto->playerId = $planet->getPlayerId();
        $planetDto->id = $planet->getId();
        $planetDto->name = $planet->getName();
        $planetDto->storage = $planet->getStorageAsPack();
        $planetDto->maxStorage = $planet->getMaxStorage();
        $planetDto->buildings = $planet->getBuildingsAsGameObjects()->toArray();
        $planetDto->hourlyProduction = $this->productionService->getHourlyProduction($planet, $this->universeSettingsRepository->getUniverseSpeed());
        $planetDto->droneAvailability = $this->droneService->getDroneAvailability($planet);
        $planetDto->energy = new EnergyDTO($this->powerService->getEnergyYield($planet), $this->powerService->getEnergyConsumption($planet));
        return $planetDto;
    }

    /**
     * @return array<string, GameObjectLevel>
     */
    public function getPlanetBuildings(int $planetId): array
    {
        $planet = $this->planetRepository->find($planetId);

        return $planet->getBuildingsAsGameObjects()->toArray();

    }

    public function planetHasResources(int $planetId, ResourcePack $pack): bool
    {
        $planet = $this->planetRepository->find($planetId);

        return $planet->hasResources($pack);
    }

    public function debitResources(int $planetId, ResourcePack $pack): void
    {
        $planet = $this->planetRepository->find($planetId);

        if (!$planet->hasResources($pack)) {
            throw new GameException("Planet has not enough resources.");
        }

        $planet->debitResources($pack);
    }

    public function creditResources(int $planetId, ResourcePack $pack): void
    {
        $planet = $this->planetRepository->find($planetId);

        $planet->creditResources($pack);
    }

}