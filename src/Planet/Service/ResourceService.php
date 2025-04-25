<?php

namespace App\Planet\Service;

use App\Exception\PlayerNotFoundException;
use App\Planet\Domain\Entity\Planet;
use App\Planet\Domain\Service\Production\ProductionService;
use App\Planet\Infrastructure\Repository\PlanetRepository;
use App\Shared\Constants;
use App\Shared\Repository\PlayerRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Shared\Application\Service\TimeService;

readonly class ResourceService
{

    public const RESOURCE_BUILDINGS = [Constants::CONCRETE_EXTRACTOR, Constants::METAL_REFINERY, Constants::POLYMER_FACTORY, Constants::HYDROPONIC_FARM];

    public function __construct(
        private PlayerRepository  $playerRepository,
        private ProductionService $productionService,
        private ManagerRegistry   $managerRegistry,
        private PlanetRepository $planetRepository,
        private TimeService $timeService
    )
    {
    }


    public function updateResourcesForUser(UserInterface $user): void
    {
        $player = $this->playerRepository->findByUser($user);
        if (!$player) {
            throw new PlayerNotFoundException($user);
        }

        $this->updateResourcesForPlayer($player);

    }

    public function updateResourcesForPlayer(int $playerId): void
    {
        $planets = $this->planetRepository->findByPlayer($playerId);
        foreach ($planets as $planet) {
            $this->updateResourcesForPlanet($planet);
        }
    }

    public function updateResourcesForPlanet(Planet $planet): void
    {


        $manager = $this->managerRegistry->getManager('world');
        $production = $this->productionService->getHourlyProduction($planet, $this->timeService->getUniverseSpeed());
        $planet->processProduction($production);
        $manager->flush();

    }


}