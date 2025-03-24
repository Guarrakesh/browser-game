<?php

namespace App\Modules\Planet\Service;

use App\Engine\Processor\ProcessorInterface;
use App\Entity\World\Player;
use App\Modules\Planet\Model\Entity\Planet;
use App\Modules\Planet\Repository\PlanetRepository;
use App\Modules\Shared\Repository\PlayerRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

readonly class ConstructionProcessor implements ProcessorInterface
{
    public function __construct(
        private Security                    $security,
        private ManagerRegistry             $managerRegistry,
        private PlayerRepository            $playerRepository,
        private PlanetRepository $planetRepository,
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
        }

        $manager->flush();
    }

    private function updateConstructionsForPlayer(int $timestamp, Player $player): void
    {
        $planets = $this->planetRepository->findByPlayer($player->getId());
        foreach ($planets as $planet) {
            $this->updateConstructionsForPlanet($timestamp, $planet);
        }
    }

    private function updateConstructionsForPlanet(int $timestamp, Planet $planet): void
    {
        $planet->processConstructions($timestamp);
    }

}