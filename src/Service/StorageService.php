<?php

namespace App\Service;

use App\Constants;
use App\Entity\World\Camp;
use App\Object\ResourcePack;
use App\ObjectRegistry\BuildingRegistry;

class StorageService
{
    public function __construct(
        private readonly BuildingRegistry        $buildingConfigurationService,
    )
    {
    }


    public function getMaxStorage(Camp $camp): int
    {
        $storageConfig = $this->buildingConfigurationService->getBuildingConfigProvider(Constants::STORAGE_BAY);

        $storageIncreaseFactor = $storageConfig->findParameter('storage_increase_factor');
        $baseStorage = $storageConfig->findParameter('base_storage');

        $bay = $camp->getBuilding(Constants::STORAGE_BAY);
        $level = min($bay->getLevel(), $storageConfig->getMaxLevel());

        return $baseStorage * ($storageIncreaseFactor ** ($level-1));

    }

    public function addResources(Camp $camp, ResourcePack $pack): void
    {
        $storage = $camp->getStorage();

        $storage->addResources($pack, $this->getMaxStorage($camp));
    }
}