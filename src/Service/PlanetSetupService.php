<?php

namespace App\Service;

use App\Exception\GameException;
use App\Modules\Planet\Infra\Registry\BuildingRegistry;
use App\Modules\Planet\Infra\Repository\PlanetRepository;
use App\Modules\Planet\Model\Entity\Planet;
use App\Modules\Planet\Model\Location;
use App\Modules\Planet\PlanetFactory;
use App\Modules\Shared\Repository\PlayerRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class PlanetSetupService
{

    public function __construct(
        private ManagerRegistry       $managerRegistry,
        private BuildingRegistry      $buildingRegistry,
        private PlanetFactory         $planetFactory,
        private PlayerRepository      $playerRepository,
        private PlanetRepository      $planetRepository,
        private TranslatorInterface   $translator,
        private TokenStorageInterface $securityStorage
    )
    {
    }

    public function createPlanet(int $playerId): Planet
    {

        $player = $this->playerRepository->find($playerId);
        if (!$player) {
            throw new GameException("Invalid Player ID #" . $playerId);
        }
        if ($this->planetRepository->playerHasPlanets($player->getId())) {
            throw new GameException("Player already has a planet.");
        }

        $entityManager = $this->managerRegistry->getManager('world');
        $planetName = $this->translator->trans('planet.default_name', ['username' => $this->securityStorage->getToken()->getUser()->getUserIdentifier()]);
        return $entityManager->wrapInTransaction(function ($entityManager) use ($player, $planetName) {
            $buildingList = $this->buildingRegistry->getStartupBuildingConfig();
            // TODO: implement location strategy
            $planet = $this->planetFactory->createNewPlanet($player->getId(), $planetName, $buildingList, new Location(0, 0, 0));

            $entityManager->persist($planet);

            return $planet;
        });

    }

}