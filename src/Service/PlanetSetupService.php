<?php

namespace App\Service;

use App\Entity\World\Player;
use App\Modules\Planet\Infra\Registry\BuildingRegistry;
use App\Modules\Planet\Model\Entity\Planet;
use App\Modules\Shared\Model\ResourcePack;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class PlanetSetupService
{

    public function __construct(
        private ManagerRegistry       $managerRegistry,
        private BuildingRegistry      $buildingRegistry,
        private TranslatorInterface   $translator,
        private TokenStorageInterface $securityStorage
    )
    {
    }

    public function createPlanet(Player $player): Planet
    {

        $entityManager = $this->managerRegistry->getManager('world');
        $planetName = $this->translator->trans('planet.default_name', ['username' => $this->securityStorage->getToken()->getUser()->getUserIdentifier()]);
        $planet = new Planet($planetName);

        $entityManager->wrapInTransaction(function ($entityManager) use ($player, $planet) {
            $date = new \DateTimeImmutable();

            // TODO: setup initial info
//            $planet->setName();
//            $planet->setCoordX(1);
//            $planet->setCoordY(1);
//            $planet->setPoints(0);
//            $planet->setActive(true);
//            $planet->setPlayer($player);


            $buildingList = $this->buildingRegistry->getStartupBuildingConfig();
            foreach ($buildingList as $name => $gameObjectLevel) {
                $buildingDefinition = $this->buildingRegistry->get($name);
                $planet->upgradeBuilding($buildingDefinition, $gameObjectLevel->getLevel());

            }

            $pack = new ResourcePack(100, 100, 100, 100);
            $planet->creditResources($pack);

            $entityManager->persist($planet);
        });

        return $planet;
    }

}