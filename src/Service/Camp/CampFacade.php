<?php

namespace App\Service\Camp;

use App\Constants;
use App\Entity\World\Camp;
use App\Service\BuildingConfigurationService;

readonly class CampFacade
{
    public function __construct(private readonly BuildingConfigurationService $buildingConfigurationService)
    {}

    public function getMaxStorage(Camp $camp): int
    {
        $storageConfig = $this->buildingConfigurationService->getBuildingConfigProvider(Constants::STORAGE_BAY);
        $bay = $camp->getBuilding(Constants::STORAGE_BAY);
        if (!$bay) {
            $maxStorage = 0;
        } else {
            $maxStorage =
                $storageConfig->getConfig('max_storage')
                * ($storageConfig->getIncreaseFactor() ** (min($bay->getLevel(),$storageConfig->getMaxLevel())-1));
        }

        return $maxStorage;
    }

    public function canBeBuilt(Camp $camp, string $buildingName, ?int $level = 1): bool
    {
        $buildingConfig = $this->buildingConfigurationService->getBuildingConfigProvider($buildingName);

        $level ??= ($camp->getBuilding($buildingName)?->getLevel() ?? 0) + 1;
        if (!$buildingConfig->areRequirementsSatisfied($camp)) {
            return false;
        }

        $storage = $camp->getStorage();
        $cost = $buildingConfig->getBaseCost()->multiply($buildingConfig->getCostFactor() ** ($level-1));

        return $storage?->containResources($cost);
    }


}