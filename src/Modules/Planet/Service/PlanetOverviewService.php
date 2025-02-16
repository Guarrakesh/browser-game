<?php

namespace App\Modules\Planet\Service;

use App\Modules\Core\Infra\Repository\UniverseSettingsRepository;
use App\Modules\Planet\Dto\PlanetDTO;
use App\Modules\Planet\Infra\Repository\PlanetRepository;
use App\Modules\Planet\Model\DomainService\Production\ProductionService;
use Symfony\Component\Stopwatch\Stopwatch;

class PlanetOverviewService
{
    public function __construct(private readonly PlanetRepository  $planetRepository,
                                private UniverseSettingsRepository $universeSettingsRepository,
                                private ProductionService          $productionService
    )
    {
    }

    public function getPlanetOverview(int $planetId): PlanetDTO
    {
        $stopwatch = new Stopwatch(true);
        $stopwatch->start('max_storage');

        $planetDto = new PlanetDTO();
        $planet = $this->planetRepository->find($planetId);

        $planetDto->id = $planet->getId();
        $planetDto->name = $planet->getName();
        $planetDto->storage = $planet->getStorageAsPack();
        $planetDto->maxStorage = $planet->getMaxStorage();
        $planetDto->buildings =  $planet->getBuildingsAsGameObjects()->toArray();
        $planetDto->hourlyProduction = $this->productionService->getHourlyProduction($planet, $this->universeSettingsRepository->getUniverseSpeed());

        $event = $stopwatch->stop('max_storage');

        return $planetDto;
    }
}