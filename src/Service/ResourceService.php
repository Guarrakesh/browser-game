<?php

namespace App\Service;

use App\Constants;
use App\Entity\World\Camp;
use App\Entity\World\Player;
use App\Exception\PlayerNotFoundException;
use App\Object\ResourcePack;
use App\ObjectRegistry\BuildingRegistry;
use App\Repository\PlayerRepository;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use LogicException;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class ResourceService
{

    public const RESOURCE_BUILDINGS =[Constants::CONCRETE_EXTRACTOR, Constants::METAL_REFINERY, Constants::CIRCUIT_ASSEMBLY_PLANT, Constants::HYDROPONIC_FARM];
    public function __construct(
        private StorageService          $storageService,
        private PlayerRepository        $playerRepository,
        private BuildingRegistry        $buildingConfigurationService,
        private ManagerRegistry         $registry)
    {
    }

    public function getHourlyProduction(Camp $camp): ResourcePack
    {
        $pack = new ResourcePack();

        foreach (self::RESOURCE_BUILDINGS as $buildingName) {
            $building = $camp->getBuilding($buildingName);
            if (!$building) {
                continue;
            }

            $config = $this->buildingConfigurationService->getBuildingConfigProvider($buildingName);
            $prodIncreaseFactor = $config->findParameter('production_increase_factor');

            // TODO: dispatch event, add univer speed
            $hourlyProduction = $config->getHourlyProduction() * ($prodIncreaseFactor ** ($building->getLevel()-1));

            $pack->addFromBuilding($buildingName, $hourlyProduction);
        }

        return $pack;
    }

    public function updateResourcesForUser(UserInterface $user): void
    {
        $player = $this->playerRepository->findByUser($user);
        if (!$player) {
            throw new PlayerNotFoundException($user);
        }

        $this->updateResourcesForPlayer($player);

    }

    public function updateResourcesForPlayer(Player $player): void
    {
        $manager = $this->registry->getManager('world');
        foreach ($player->getCamps() as $camp) {
            $this->updateResourcesForCamp($camp, false);
        }
        $manager->flush();
    }

    public function updateResourcesForCamp(Camp $camp, bool $flush): void
    {
        $manager = $this->registry->getManager('world');

        $storage = $camp->getStorage();
        if (!$storage) {
            throw new LogicException(sprintf("Camp %s has no storage.", $camp->getId()));
        }

        $maxStorage = $this->storageService->getMaxStorage($camp);
        $now = new DateTimeImmutable();
        $lastUpdate = $storage->getUpdatedAt();
        $elapsedSeconds = $now->getTimestamp() - $lastUpdate?->getTimestamp();

        $production = $this->getHourlyProduction($camp)
            ->toSeconds()
            ->multiply($elapsedSeconds);

        $storage->addResources($production, $maxStorage);
        $storage->setUpdatedAt($now);

        if ($flush) {
            $manager->flush();
        }
    }


}