<?php

namespace App\Service;

use App\Entity\World\Player;
use App\Entity\World\Storage;
use App\Modules\Core\Entity\Planet;
use App\Modules\Core\Entity\PlanetBuilding;
use App\ObjectRegistry\BuildingRegistry;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class PlanetSetupService
{

    public function __construct(
        private ManagerRegistry       $managerRegistry,
        private BuildingRegistry      $buildingConfigurationService,
        private TranslatorInterface   $translator,
        private TokenStorageInterface $securityStorage
    )
    {
    }

    public function createPlanet(Player $player): Planet {

        $entityManager = $this->managerRegistry->getManager('world');
        $planet = new Planet();

        $entityManager->wrapInTransaction(function ($entityManager) use ($player, $planet) {
            $date = new \DateTimeImmutable();

            $planet->setName($this->translator->trans('planet.default_name', ['username' => $this->securityStorage->getToken()->getUser()->getUserIdentifier()]));
            $planet->setCoordX(1);
            $planet->setCoordY(1);
            $planet->setPoints(0);
            $planet->setActive(true);
            $planet->setPlayer($player);


            $buildingList = $this->buildingConfigurationService->getStartupBuildingConfig();
            foreach ($buildingList as $name => $buildingLevel) {
                $building = new PlanetBuilding();
                $building->setName($name);
                $building->setPlanet($planet);
                $building->setLevel($buildingLevel);
                $building->setUpdatedAt($date);

                $planet->addPlanetBuilding($building);
                $entityManager->persist($building);
            }

            $storage = new Storage();
            $storage->setConcrete(100);
            $storage->setMetals(100);
            $storage->setCircuits(100);
            $storage->setFood(100);
            $storage->setUpdatedAt($date);

            $planet->setStorage($storage);

            $entityManager->persist($storage);
            $entityManager->persist($planet);
        });

        return $planet;
    }

}