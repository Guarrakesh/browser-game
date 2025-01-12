<?php

namespace App\Camp;

use App\Constants;
use App\CurveCalculator\CurveCalculatorProvider;
use App\Entity\World\Camp;
use App\Event\BuildingCostEvent;
use App\Model\ResourcePack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class CampFacade
{
    public function __construct(
        private CurveCalculatorProvider $curveCalculatorProvider,
        private EventDispatcherInterface $dispatcher,
        private BuildingConfigurationService $buildingConfigurationService)
    {}

    public function getMaxStorage(Camp $camp): int
    {
        $storageConfig = $this->buildingConfigurationService->getBuildingConfigProvider(Constants::STORAGE_BAY);
        $calcConfig = $storageConfig->getCalculatorConfig('production_calculator');
        $calculator = $this->curveCalculatorProvider->getCalculator($calcConfig->id);

        $bay = $camp->getBuilding(Constants::STORAGE_BAY);
        return $calculator->calculateForLevel(min($bay->getLevel(), $storageConfig->getMaxLevel()), $storageConfig->getConfig('max_storage'), $calcConfig->parameters);


    }

    /**
     * TODO: find a way to cache these calculations, using a cache adapter and memoization.
     */
    public function getCostForBuilding(Camp $camp, string $buildingName, ?int $level = null): ResourcePack
    {

        $buildingConfig = $this->buildingConfigurationService->getBuildingConfigProvider($buildingName);
        $level ??= $camp->getNextLevelForBuilding($buildingName);
        $calcConfig = $buildingConfig->getCalculatorConfig('cost_calculator');
        $calculator = $this->curveCalculatorProvider->getCalculator($calcConfig->id);


        $cost = $buildingConfig->getBaseCost()->map(
            fn ($baseCost) => $calculator->calculateForLevel($level, $baseCost, $calcConfig->parameters)
        );

        $event = new BuildingCostEvent($camp, $buildingConfig, $level, $cost);
        $this->dispatcher->dispatch($event);

        return $event->getCost();
    }

    public function canBeBuilt(Camp $camp, string $buildingName, ?int $level = null, ?ResourcePack $cost = null): bool
    {
        $buildingConfig = $this->buildingConfigurationService->getBuildingConfigProvider($buildingName);

        $level ??= $camp->getNextLevelForBuilding($buildingName);
        if (!$buildingConfig->areRequirementsSatisfied($camp)) {
            return false;
        }

        $storage = $camp->getStorage();

        $cost ??= $this->getCostForBuilding($camp, $buildingName, $level);
        return $storage?->containResources($cost);
    }

    /**
     * @return int The time in seconds to build the building at the given or latest level
     */
    public function getBuildTime(Camp $camp, string $buildingName, ?int $level = null): int
    {
        $level ??= $camp->getNextLevelForBuilding($buildingName);
        $buildingConfig = $this->buildingConfigurationService->getBuildingConfigProvider($buildingName);
        $calcConfig = $buildingConfig->getCalculatorConfig('build_time_calculator');
        $calculator = $this->curveCalculatorProvider->getCalculator($calcConfig->id);

        return $calculator->calculateForLevel($level, $buildingConfig->getBaseBuildTime(), $calcConfig->parameters);
    }


}