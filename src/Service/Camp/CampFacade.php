<?php

namespace App\Service\Camp;

use App\Constants;
use App\CurveCalculator\CurveCalculatorProvider;
use App\Entity\World\Camp;
use App\Service\BuildingConfigurationService;

readonly class CampFacade
{
    public function __construct(
        private readonly CurveCalculatorProvider $curveCalculatorProvider,
        private readonly BuildingConfigurationService $buildingConfigurationService)
    {}

    public function getMaxStorage(Camp $camp): int
    {
        $storageConfig = $this->buildingConfigurationService->getBuildingConfigProvider(Constants::STORAGE_BAY);
        $calcConfig = $storageConfig->getCalculatorConfig('production_calculator');
        if (!$calcConfig) {
            throw new \LogicException(sprintf("Expected to find production_calculator config in building %s", Constants::STORAGE_BAY));
        }
        $calculator = $this->curveCalculatorProvider->getCalculator($calcConfig->id);

        $bay = $camp->getBuilding(Constants::STORAGE_BAY);
        return $calculator->calculateForLevel(min($bay->getLevel(), $storageConfig->getMaxLevel())-1, $storageConfig->getConfig('max_storage'), $calcConfig->parameters);


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