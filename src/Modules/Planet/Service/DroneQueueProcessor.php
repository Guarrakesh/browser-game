<?php

namespace App\Modules\Planet\Service;

use App\Engine\Processor\ProcessorInterface;
use App\Entity\World\Player;
use App\Modules\Planet\Infra\Repository\DroneQueueRepository;
use App\Modules\Planet\Infra\Repository\PlanetRepository;
use App\Modules\Planet\Model\Entity\Planet;
use App\Modules\Shared\Repository\PlayerRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

readonly class DroneQueueProcessor implements ProcessorInterface
{
    public function __construct(
        private Security         $security,
        private ManagerRegistry  $managerRegistry,
        private PlayerRepository $playerRepository,
        private PlanetRepository $planetRepository, private DroneQueueRepository $droneQueueRepository,
    )
    {
    }

    public function process(int $timestamp): void
    {
        $manager = $this->managerRegistry->getManager('world');

        $user = $this->security->getUser();
        if ($user) {
            $player = $this->playerRepository->findByUser($user);
            $this->updateDronesForPlayer($timestamp, $player);
        }

        $manager->flush();
    }

    private function updateDronesForPlayer(int $timestamp, Player $player): void
    {
        $planets = $this->planetRepository->findByPlayer($player->getId());
        foreach ($planets as $planet) {
            $this->updateDronesForPlanet($timestamp, $planet);
        }
    }

    private function updateDronesForPlanet(int $timestamp, Planet $planet): void
    {
        $queue = $this->droneQueueRepository->getDroneQueue($planet->getId());
        foreach ($queue->processCompletedJobs($timestamp) as $job) {
            $planet->addDrone();
        }

    }

}