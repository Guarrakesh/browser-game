<?php

namespace App\Service;

use App\Constants;
use App\Entity\World\Player;
use App\Exception\PlayerNotFoundException;
use App\Modules\Core\Entity\Planet;
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

    public function getHourlyProduction(Planet $planet): ResourcePack
    {
        $pack = new ResourcePack();

        foreach (self::RESOURCE_BUILDINGS as $buildingName) {
            $building = $planet->getBuilding($buildingName);
            if (!$building) {
                continue;
            }

            $config = $this->buildingConfigurationService->get($buildingName);
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
        foreach ($player->getPlanets() as $planet) {
            $this->updateResourcesForPlanet($planet, false);
        }
        $manager->flush();
    }

    public function updateResourcesForPlanet(Planet $planet, bool $flush): void
    {
        $manager = $this->registry->getManager('world');

        $storage = $planet->getStorage();
        if (!$storage) {
            throw new LogicException(sprintf("Planet %s has no storage.", $planet->getId()));
        }

        $maxStorage = $this->storageService->getMaxStorage($planet);
        $now = new DateTimeImmutable();
        $lastUpdate = $storage->getUpdatedAt();
        $elapsedSeconds = $now->getTimestamp() - $lastUpdate?->getTimestamp();

        $production = $this->getHourlyProduction($planet)
            ->toSeconds()
            ->multiply($elapsedSeconds);

        $storage->addResources($production, $maxStorage);
        $storage->setUpdatedAt($now);

        if ($flush) {
            $manager->flush();
        }
    }


}