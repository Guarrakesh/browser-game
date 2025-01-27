<?php

namespace App\Service;

use App\Constants;
use App\Helper\TransactionTrait;
use App\Modules\Core\Entity\Planet;
use App\Object\ResourcePack;
use App\ObjectRegistry\BuildingRegistry;
use Doctrine\Persistence\ManagerRegistry;

class StorageService
{
    use TransactionTrait;

    public function __construct(
        private readonly BuildingRegistry $buildingConfigurationService, private readonly ManagerRegistry $managerRegistry,
    )
    {
    }


    public function getMaxStorage(Planet $planet): int
    {
        $storageConfig = $this->buildingConfigurationService->get(Constants::STORAGE_BAY);

        $storageIncreaseFactor = $storageConfig->findParameter('storage_increase_factor');
        $baseStorage = $storageConfig->findParameter('base_storage');

        $bay = $planet->getBuilding(Constants::STORAGE_BAY);
        $level = min($bay->getLevel(), $storageConfig->getMaxLevel());

        return $baseStorage * ($storageIncreaseFactor ** ($level - 1));
    }

    public function addResources(Planet $planet, ResourcePack $pack): void
    {
        $planet->getStorage()->addResources($pack, $this->getMaxStorage($planet));
    }
}