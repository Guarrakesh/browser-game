<?php

namespace App\Modules\Construction\Service;

use App\Entity\World\Queue\PlanetConstruction;
use App\Entity\World\Queue\Queue;
use App\Exception\GameException;
use App\Exception\InsufficientResourcesException;
use App\Helper\QueueUtil;
use App\Helper\TransactionTrait;
use App\Model\PlanetBuildingList;
use App\Modules\Construction\DTO\ConstructionDTO;
use App\Modules\Construction\DTO\ConstructionQueueDTO;
use App\Modules\Construction\Entity\ConstructionLog;
use App\Modules\Core\DTO\PlanetDTO;
use App\Modules\Core\Entity\Planet;
use App\Modules\Core\Repository\PlanetRepository;
use App\Object\ResourcePack;
use App\ObjectRegistry\BuildingRegistry;
use App\Repository\PlanetConstructionRepository;
use App\Service\Cost\CostService;
use App\Service\ObjectTime\ObjectTimeService;
use App\Service\StorageService;
use AutoMapper\AutoMapperInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Throwable;

class ConstructionService
{

    use TransactionTrait;

    /** @var array<int,Queue<PlanetConstruction>> */
    private array $planetQueues = [];

    public function __construct(
        private readonly AutoMapperInterface          $autoMapper,
        private readonly ManagerRegistry              $managerRegistry,
        private readonly PlanetConstructionRepository $planetConstructionRepository,
        private readonly BuildingRegistry             $buildingRegistry,
        private readonly CostService                  $costService,
        private readonly StorageService               $storageService,
        private readonly ObjectTimeService            $objectTimeService,
        private readonly PlanetRepository             $planetRepository,
    )
    {
    }


    /**
     * @param Planet $planet
     * @param string $buildingName
     * @return void
     */
    public function enqueueConstruction(Planet $planet, string $buildingName): Queue
    {
        $manager = $this->managerRegistry->getManager('world');
        $buildTime = $this->getBuildTime($planet, $buildingName);

        $queue = $this->getConstructionQueue($planet);

        $storage = $planet->getStorage();
        $callback = function () use ($manager, $storage, $buildingName, $planet, $buildTime, $queue) {
            $manager->refresh($storage, LockMode::PESSIMISTIC_WRITE);
            $manager->refresh($planet, LockMode::PESSIMISTIC_READ);

            $cost = $this->getCostForBuilding($planet, $buildingName);

            if (!$this->canBeBuilt($planet, $buildingName, null, $cost)) {
                throw new InsufficientResourcesException($cost);
            }
            $this->storageService->addResources($planet, $cost->multiply(-1));

            $construction = new PlanetConstruction();
            $construction->setPlanet($planet);
            $construction->setBuildingName($buildingName);
            $construction->setResourcesUsed($cost);

            $construction->setLevel($this->getNextLevelForBuilding($planet, $buildingName));
            $queue->enqueueJob($construction, $buildTime);

            $manager->persist($construction);
            $manager->persist($planet);
            $this->persistQueue($planet);

            return $queue;
        };

        try {
            // TODO: investigate if this is the right approach or we should just try the transaction once.
            return $this->transactionalRetry($this->managerRegistry, 'world', $callback, 2);

        } catch (Throwable $exception) {

            throw new GameException(sprintf("Could not enqueue the construction: %s. Try again.", $exception->getMessage()), 0, $exception);
        }
    }


    public function cancelConstruction(PlanetConstruction $construction): void
    {
        $manager = $this->managerRegistry->getManager('world');

        if (!$construction->getId()) {
            throw new GameException("ConstructionDTO is not persisted.");
        }

        $planet = $construction->getPlanet();
        $queue = $this->getConstructionQueue($construction->getPlanet());

        $callback = function () use ($manager, $queue, $construction, $planet) {
            $manager->lock($planet->getStorage(), LockMode::PESSIMISTIC_WRITE);
            $queue->cancelJob($construction);
            $log = ConstructionLog::fromCancelled($construction);
            $this->persistQueue($construction->getPlanet());

            $construction->setCancelledAt(new \DateTimeImmutable());
            $this->storageService->addResources($planet, $construction->getResourcesUsed());


            $manager->persist($log);

        };
        try {
            $this->transactionalRetry($this->managerRegistry, 'world', $callback);
        } catch (Exception $e) {
            throw new GameException("Could not cancel constructions. Try again.", 0, $e);
        }

        $manager->flush();
    }

    public function getNextLevelForBuilding(Planet $planet, string $buildingName): int
    {
        $queue = $this->getConstructionQueue($planet);

        /** @var Queue<PlanetConstruction> $existingBuildingJobs */
        $existingBuildingJobs = QueueUtil::filter($queue, fn(PlanetConstruction $job) => $job->getBuildingName() === $buildingName);
        if (!empty($existingBuildingJobs)) {
            return $existingBuildingJobs[count($existingBuildingJobs) - 1]->getLevel() + 1;
        }


        return ($planet->getBuilding($buildingName)?->getLevel() ?? 0) + 1;
    }

    public function getConstructionQueue(Planet $planet): Queue
    {
        if (!isset($this->planetQueues[$planet->getId()])) {
            $this->planetQueues[$planet->getId()] = $this->planetConstructionRepository->getConstructionQueue($planet);
        }

        return $this->planetQueues[$planet->getId()];
    }

    private function persistQueue(Planet $planet): void
    {
        if (!isset($this->planetQueues[$planet->getId()])) {
            return;
        }

        $manager = $this->managerRegistry->getManager('world');

        QueueUtil::forEach(
            $this->planetQueues[$planet->getId()],
            fn(PlanetConstruction $planetConstruction) => $manager->persist($planetConstruction));

    }


    /**
     * TODO: find a way to cache these calculations, using a cache adapter and memoization.
     */
    public function getCostForBuilding(Planet $planet, string $buildingName, ?int $level = null): ResourcePack
    {

        $buildingConfig = $this->buildingRegistry->get($buildingName);
        $level ??= $this->getNextLevelForBuilding($planet, $buildingName);

        return $this->costService->getCostForObject($planet, $buildingConfig, $level);
    }

    public function canBeBuilt(Planet $planet, string $buildingName, ?int $level = null, ?ResourcePack $cost = null): bool
    {
        $buildingConfig = $this->buildingRegistry->get($buildingName);

        $level ??= $this->getNextLevelForBuilding($planet, $buildingName);
        if (!$buildingConfig->areRequirementsSatisfied($planet)) {
            return false;
        }

        $storage = $planet->getStorage();

        $cost ??= $this->getCostForBuilding($planet, $buildingName, $level);
        return $storage?->containResources($cost);
    }

    public function areConstructionRequirementsMet(Planet $planet, string $buildingName, ?int $level = null): bool
    {
        $level ??= $this->getNextLevelForBuilding($planet, $buildingName);
        $buildingDef = $this->buildingRegistry->get($buildingName);

        return $buildingDef->getRequirements()->isSatisfied(PlanetBuildingList::fromPlanet($planet));
    }

    /**
     * @return int The time in seconds to build the building at the given or latest level
     */
    public function getBuildTime(Planet $planet, string $buildingName, ?int $level = null): int
    {
        $level ??= $this->getNextLevelForBuilding($planet, $buildingName);
        $buildingConfig = $this->buildingRegistry->get($buildingName);

        return $this->objectTimeService->getTimeForObject($planet, $buildingConfig, $level);

    }

    /**
     * Returns all possible constructions for this planet, including the ones for which requirements are not met
     * or for which the planet has no resources available
     * @return array<ConstructionDTO>
     */
    public function getPossibleConstructions(Planet $planet): array
    {
        $result = [];
        foreach ($this->buildingRegistry->getIterator() as $buildingDefinition) {
            $construction = new ConstructionDTO();
            $construction->buildingName = $buildingDefinition->getName();
            $construction->level = $this->getNextLevelForBuilding($planet, $buildingDefinition->getName());
            $cost = $this->costService->getCostForObject($planet, $buildingDefinition, $construction->level);
            $construction->areRequirementsMet = $this->areConstructionRequirementsMet($planet, $buildingDefinition->getName(), $construction->level);
            $construction->requirements = $buildingDefinition->getRequirements()->getRequiredBuildings();
            if ($construction->areRequirementsMet) {
                // Do not expose cost and build time if requirements are not met.
                $construction->isCostSatisfied = $planet->getStorage()->containResources($cost);
                $construction->cost = $cost;
                $construction->buildTime = $this->objectTimeService->getTimeForObject($planet, $buildingDefinition, $construction->level);
            }
            $construction->canBeBuilt = $construction->areRequirementsMet && $construction->isCostSatisfied;
            $result[$buildingDefinition->getName()] = $construction;
        }

        return $result;
    }

}