<?php

namespace App\Service;

use App\Camp\StorageService;
use App\CurveCalculator\CurveCalculatorProvider;
use App\Entity\World\Camp;
use App\Entity\World\ConstructionLog;
use App\Entity\World\Queue\CampConstruction;
use App\Entity\World\Queue\Queue;
use App\Event\BuildingCostEvent;
use App\Exception\GameException;
use App\Exception\InsufficientResourcesException;
use App\Helper\DBUtils;
use App\Helper\QueueUtil;
use App\Object\ResourcePack;
use App\ObjectRegistry\BuildingRegistry;
use App\Repository\CampConstructionRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Psr\EventDispatcher\EventDispatcherInterface;
use Throwable;

class ConstructionService
{

    /** @var array<int,Queue<CampConstruction>> */
    private array $campQueues = [];
    public function __construct(
        private readonly ManagerRegistry            $managerRegistry,
        private readonly CampConstructionRepository $campConstructionRepository,
        private readonly BuildingRegistry           $buildingConfigurationService,
        private readonly CurveCalculatorProvider    $curveCalculatorProvider,
        private readonly StorageService             $storageService
    )
    {
    }


    /**
     * @param Camp $camp
     * @param string $buildingName
     * @return void
     */
    public function enqueueConstruction(Camp $camp, string $buildingName): void
    {

        $manager = $this->managerRegistry->getManager('world');
        $buildTime = $this->getBuildTime($camp, $buildingName);

        $queue = $this->getCampQueue($camp);

        $storage = $camp->getStorage();
        $callback = function () use ($manager, $storage, $buildingName, $camp, $buildTime, $queue) {
            $manager->lock($storage, LockMode::PESSIMISTIC_WRITE);

            $cost = $this->getCostForBuilding($camp, $buildingName);

            if (!$this->canBeBuilt($camp, $buildingName, null, $cost)) {
                throw new InsufficientResourcesException($cost);
            }
            $this->storageService->addResources($camp, $cost->multiply(-1));

            $construction = new CampConstruction();
            $construction->setCamp($camp);
            $construction->setBuildingName($buildingName);
            $construction->setResourcesUsed($cost);

            $construction->setLevel($this->getNextLevelForBuilding($camp, $buildingName));
            $queue->enqueueJob($construction, $buildTime);

            $manager->persist($construction);
            $manager->persist($camp);
            $this->persistQueue($camp);
        };

        try {
            DBUtils::transactionalRetry($this->managerRegistry, 'world', $callback);
        } catch (Throwable $exception) {

            throw new GameException(sprintf("Could not enqueue the construction: %s. Try again.", $exception->getMessage()), 0, $exception);
        }
    }

    public function cancelConstruction(CampConstruction $construction): void
    {
        $manager = $this->managerRegistry->getManager('world');

        if (!$construction->getId()) {
            throw new GameException("Construction is not persisted.");
        }

        $camp = $construction->getCamp();
        $queue = $this->getCampQueue($construction->getCamp());
        try {
            $queue->cancelJob($construction);
            $log = ConstructionLog::fromCancelled($construction);
            $this->persistQueue($construction->getCamp());

            $this->storageService->addResources($camp, $construction->getResourcesUsed());

            $manager->persist($log);
        } catch (Exception $e) {
            throw new GameException("Could not cancel constructions. Try again.", 0, $e);
        }

        $manager->remove($construction);
        $manager->flush();
    }

    public function getNextLevelForBuilding(Camp $camp, string $buildingName): int
    {
        $queue = $this->getCampQueue($camp);

        /** @var Queue<CampConstruction> $existingBuildingJobs */
        $existingBuildingJobs = QueueUtil::filter($queue, fn (CampConstruction $job) => $job->getBuildingName() === $buildingName);
        if (!empty($existingBuildingJobs)) {
           return $existingBuildingJobs[count($existingBuildingJobs) - 1]->getLevel() + 1;
        }


        return ($camp->getBuilding($buildingName)?->getLevel() ?? 0) + 1;
    }

    public function getCampQueue(Camp $camp): Queue
    {
        if (!isset($this->campQueues[$camp->getId()])) {
            $this->campQueues[$camp->getId()] = $this->campConstructionRepository->getConstructionQueue($camp);
        }

        return $this->campQueues[$camp->getId()];
    }

    private function persistQueue(Camp $camp): void
    {
        if (!isset($this->campQueues[$camp->getId()])) {
            return;
        }

        $manager = $this->managerRegistry->getManager('world');

        QueueUtil::forEach(
            $this->campQueues[$camp->getId()],
            fn (CampConstruction $campConstruction) => $manager->persist($campConstruction));

    }


    /**
     * TODO: find a way to cache these calculations, using a cache adapter and memoization.
     */
    public function getCostForBuilding(Camp $camp, string $buildingName, ?int $level = null): ResourcePack
    {

        $buildingConfig = $this->buildingConfigurationService->getBuildingConfigProvider($buildingName);
        $level ??= $this->getNextLevelForBuilding($camp, $buildingName);
        $calcConfig = $buildingConfig->getCalculatorConfig('cost_calculator');
        $calculator = $this->curveCalculatorProvider->getCalculator($calcConfig->id);


        $cost = $buildingConfig->getBaseCost()->map(
            fn ($baseCost) => $calculator->calculateForLevel($level, $baseCost, $calcConfig->parameters)
        );

        // TODO: don't like to dispatch an event in a get() method. Smells of Side effect. Find another way to compose cost calculation, maybe through a dedicated service.
        //$event = new BuildingCostEvent($camp, $buildingConfig, $level, $cost);
        //$this->dispatcher->dispatch($event);

     //   return $event->getCost();

        return $cost;
    }

    public function canBeBuilt(Camp $camp, string $buildingName, ?int $level = null, ?ResourcePack $cost = null): bool
    {
        $buildingConfig = $this->buildingConfigurationService->getBuildingConfigProvider($buildingName);

        $level ??= $this->getNextLevelForBuilding($camp, $buildingName);
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
        $level ??= $this->getNextLevelForBuilding($camp, $buildingName);
        $buildingConfig = $this->buildingConfigurationService->getBuildingConfigProvider($buildingName);
        $calcConfig = $buildingConfig->getCalculatorConfig('build_time_calculator');
        $calculator = $this->curveCalculatorProvider->getCalculator($calcConfig->id);

        return $calculator->calculateForLevel($level, $buildingConfig->getBaseBuildTime(), $calcConfig->parameters);
    }

}