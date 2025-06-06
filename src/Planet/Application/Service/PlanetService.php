<?php
namespace App\Planet\Application\Service;

use App\Energy\Application\Service\EnergyService;
use App\Exception\GameException;
use App\Planet\Domain\Service\Production\ProductionService;
use App\Planet\Dto\EnergyDTO;
use App\Planet\Dto\PlanetDTO;
use App\Planet\Infrastructure\Repository\DroneAllocationRepository;
use App\Planet\Infrastructure\Repository\PlanetRepository;
use App\Planet\Service\DroneService;
use App\Shared\Application\Service\TimeService;
use App\Shared\Dto\GameObjectLevel;
use App\Shared\Model\ResourcePack;


readonly class PlanetService
{
    public function __construct(private PlanetRepository  $planetRepository,
                                private ProductionService $productionService,
                                private DroneService      $droneService,
                                private EnergyService     $powerService,
                                private TimeService       $timeService, private DroneAllocationRepository $droneAllocationRepository,
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
        $planetDto->mines = $planet->getMinesAsGameObjects()->toArray();
        $planetDto->hourlyProduction = $this->productionService->getHourlyProduction($planet, $this->timeService->getUniverseSpeed());
        $planetDto->droneAvailability = $this->droneService->getDroneAvailability($planet);
        $planetDto->energy = new EnergyDTO($this->powerService->getEnergyYield($planet), $this->powerService->getEnergyConsumption($planet));
        $planetDto->droneAllocations = $this->droneAllocationRepository->findByPlanet($planetId);

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