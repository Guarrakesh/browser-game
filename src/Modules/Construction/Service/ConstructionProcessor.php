<?php

namespace App\Modules\Construction\Service;

use App\Engine\Processor\ProcessorInterface;
use App\Entity\World\Player;
use App\Entity\World\Queue\PlanetConstruction;

use App\Modules\Construction\Entity\ConstructionLog;
use App\Modules\Construction\Event\ConstructionCompletedEvent;
use App\Modules\Core\Entity\Planet;
use App\Modules\Core\Entity\PlanetBuilding;

use App\Repository\PlanetConstructionRepository;
use App\Repository\PlayerRepository;
use Doctrine\Persistence\ManagerRegistry;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\SecurityBundle\Security;

readonly class ConstructionProcessor implements ProcessorInterface
{
    public function __construct(
        private Security                     $security,
        private ManagerRegistry              $managerRegistry,
        private PlayerRepository             $playerRepository,
        private PlanetConstructionRepository $planetConstructionRepository,
        private EventDispatcherInterface     $dispatcher,
    )
    {
    }

    public function process(int $timestamp): void
    {
        $manager = $this->managerRegistry->getManager('world');

        $user = $this->security->getUser();
        if ($user) {
            $player = $this->playerRepository->findByUser($user);
            $this->updateConstructionsForPlayer($timestamp, $player);
            return;
        }

        $constructions = $this->planetConstructionRepository->getCompletedConstructions($timestamp);
        foreach ($constructions as $construction) {
            $this->processConstruction($timestamp, $construction);

        }

        $manager->flush();
    }



    private function processConstruction(int $timestamp, PlanetConstruction $construction): void
    {
        $manager = $this->managerRegistry->getManager('world');

        if ($construction->getCompletedAt()->getTimestamp() > $timestamp) {
            return;
        }

        $planet = $construction->getPlanet();
        $log = ConstructionLog::fromCompleted($construction);

        $building = $planet->getBuilding($construction->getBuildingName());
        if (!$building) {
            $building = new PlanetBuilding();
            $building->setName($construction->getBuildingName());
            $building->setPlanet($planet);
            $planet->addplanetBuilding($building);
        }
        $building->setLevel($construction->getLevel());

        $manager->persist($building);
        $manager->persist($log);

        $this->dispatcher->dispatch(new ConstructionCompletedEvent($construction));

    }

    private function updateConstructionsForPlayer(int $timestamp, Player $player): void
    {
        $manager = $this->managerRegistry->getManager('world');

        foreach ($player->getPlanets() as $planet) {
            $this->updateConstructionsForPlanet($timestamp, $planet);
        }
        $manager->flush();


    }

    private function updateConstructionsForPlanet(int $timestamp, Planet $planet): void
    {
        $constructions = $this->planetConstructionRepository->getCompletedConstructions($timestamp, $planet);
        foreach ($constructions as $construction) {
            $this->processConstruction($timestamp, $construction);
        }

    }

}