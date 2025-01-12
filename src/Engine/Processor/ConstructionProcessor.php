<?php

namespace App\Engine\Processor;

use App\Entity\World\Camp;
use App\Entity\World\CampBuilding;
use App\Entity\World\ConstructionLog;
use App\Entity\World\Player;
use App\Entity\World\Queue\CampConstruction;
use App\Event\Construction\ConstructionCompletedEvent;
use App\Repository\CampConstructionRepository;
use App\Repository\PlayerRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\SecurityBundle\Security;

readonly class ConstructionProcessor implements ProcessorInterface
{
    public function __construct(
        private Security                   $security,
        private ManagerRegistry            $managerRegistry,
        private PlayerRepository           $playerRepository,
        private CampConstructionRepository $campConstructionRepository,
        private EventDispatcherInterface $dispatcher,
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

        $constructions = $this->campConstructionRepository->getCompletedConstructions($timestamp);
        foreach ($constructions as $construction) {
            $this->processConstruction($timestamp, $construction);

        }

        $manager->flush();
    }



    private function processConstruction(int $timestamp, CampConstruction $construction): void
    {
        $manager = $this->managerRegistry->getManager('world');

        if ($construction->getCompletedAt()->getTimestamp() > $timestamp) {
            return;
        }

        $camp = $construction->getCamp();
        $log = ConstructionLog::fromCompleted($construction);

        $building = $camp->getBuilding($construction->getBuildingName());
        if (!$building) {
            $building = new CampBuilding();
            $building->setName($construction->getBuildingName());
            $building->setCamp($camp);
            $camp->addCampBuilding($building);
        }
        $building->setLevel($construction->getLevel());

        $manager->persist($building);
        $manager->persist($log);
        $manager->remove($construction);

        $this->dispatcher->dispatch(new ConstructionCompletedEvent($construction));

    }

    private function updateConstructionsForPlayer(int $timestamp, Player $player): void
    {
        $manager = $this->managerRegistry->getManager('world');

        foreach ($player->getCamps() as $camp) {
            $this->updateConstructionsForCamp($timestamp, $camp);
        }
        $manager->flush();


    }

    private function updateConstructionsForCamp(int $timestamp, Camp $camp): void
    {
        $constructions = $this->campConstructionRepository->getCompletedConstructions($timestamp, $camp);
        foreach ($constructions as $construction) {
            $this->processConstruction($timestamp, $construction);
        }

    }

}