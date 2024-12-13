<?php

namespace App\Service\Camp;

use App\Entity\World\Camp;
use App\Entity\World\CampBuilding;
use App\Entity\World\Player;
use App\Entity\World\Storage;
use App\Service\BuildingConfigurationService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class CampSetupService
{

    public function __construct(
        private ManagerRegistry              $managerRegistry,
        private BuildingConfigurationService $buildingConfigurationService,
        private TranslatorInterface          $translator,
        private TokenStorageInterface        $securityStorage
    )
    {
    }

    public function createCamp(Player $player): Camp {

        $entityManager = $this->managerRegistry->getManager('world');
        $camp = new Camp();

        $entityManager->wrapInTransaction(function ($entityManager) use ($player, $camp) {
            $date = new \DateTimeImmutable();

            $camp->setName($this->translator->trans('camp.default_name', ['username' => $this->securityStorage->getToken()->getUser()->getUserIdentifier()]));
            $camp->setCoordX(1);
            $camp->setCoordY(1);
            $camp->setPoints(0);
            $camp->setActive(true);
            $camp->setPlayer($player);


            $buildingList = $this->buildingConfigurationService->getStartupBuildingConfig();
            foreach ($buildingList as $name => $buildingLevel) {
                $building = new CampBuilding();
                $building->setType($name);
                $building->setCamp($camp);
                $building->setLevel($buildingLevel);
                $building->setUpdatedAt($date);

                $camp->addCampBuilding($building);
                $entityManager->persist($building);
            }

            $campStorage = new Storage();
            $campStorage->setConcrete(100);
            $campStorage->setMetals(100);
            $campStorage->setCircuits(100);
            $campStorage->setFood(100);
            $campStorage->setUpdatedAt($date);

            $camp->setStorage($campStorage);

            $entityManager->persist($campStorage);
            $entityManager->persist($camp);
        });

        return $camp;
    }

}