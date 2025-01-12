<?php

namespace App\Camp;

use App\Constants;
use App\CurveCalculator\CurveCalculatorProvider;
use App\Entity\World\Camp;
use App\Model\ResourcePack;

class StorageService
{
    public function __construct(
        private readonly BuildingConfigurationService $buildingConfigurationService,
        private readonly CurveCalculatorProvider $curveCalculatorProvider
    )
    {
    }


    public function getMaxStorage(Camp $camp): int
    {
        $storageConfig = $this->buildingConfigurationService->getBuildingConfigProvider(Constants::STORAGE_BAY);
        $calcConfig = $storageConfig->getCalculatorConfig('production_calculator');
        $calculator = $this->curveCalculatorProvider->getCalculator($calcConfig->id);

        $bay = $camp->getBuilding(Constants::STORAGE_BAY);
        return $calculator->calculateForLevel(min($bay->getLevel(), $storageConfig->getMaxLevel()), $storageConfig->getConfig('max_storage'), $calcConfig->parameters);

    }

    public function addResources(Camp $camp, ResourcePack $pack): void
    {
        $storage = $camp->getStorage();

        $storage->addResources($pack, $this->getMaxStorage($camp));
    }
}