<?php

namespace App\Modules\Planet\Service;

use App\Modules\Planet\Dto\ConstructionDTO;
use App\Modules\Planet\Dto\ConstructionQueueJobDTO;
use App\Modules\Planet\Dto\ControlHubDTO;
use App\Modules\Planet\Dto\DroneQueueJobDTO;
use App\Modules\Planet\GameObject\Building\BuildingDefinition;
use App\Modules\Planet\Model\Entity\Drone\DroneQueueJob;
use App\Modules\Planet\Model\Entity\Planet;
use App\Modules\Planet\Model\Entity\PlanetConstruction;
use App\Modules\Planet\Repository\PlanetRepository;
use App\Modules\Shared\Dto\GameObjectWithRequirements;
use App\Modules\Shared\ObjectTime\TimeService;
use App\Modules\Shared\Service\Cost\CostCalculator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

readonly class ControlHubService
{

    public function __construct(
        private CostCalculator   $costCalculator,
        private BuildingRegistry $buildingRegistry,
        private PlanetRepository $planetRepository,
        private DroneService     $droneService,
        private TimeService      $objectTimeService,
        private ManagerRegistry  $managerRegistry,
        private Security         $security, private EnergyService $energyService
    )
    {
    }

    public function getControlHubOverview(int $planetId): ControlHubDTO
    {
        $planet = $this->planetRepository->find($planetId);

        $controlHubDto = new ControlHubDTO();
        $controlHubDto->possibleConstructions = $this->getPossibleConstructions($planet);
        $controlHubDto->buildings = $planet->getBuildingsAsGameObjects()->toArray();


        $controlHubDto->lockedConstructions = array_map(
            static fn(BuildingDefinition $definition) => new GameObjectWithRequirements($definition->getAsGameObject(), $definition->getRequirements()),
            array_filter(
                $this->buildingRegistry->getAll(),
                static fn(BuildingDefinition $building) => !$planet->areBuildingRequirementsMet($building)
            )
        );

        $controlHubDto->queuedJobs = $planet->getQueuedJobs()->map(
            fn(PlanetConstruction $construction) => new ConstructionQueueJobDTO(
                $construction->getId(),
                $construction->getBuildingName(),
                $construction->getLevel(),
                $construction->getDuration(),
                $construction->getStartedAt(),
                $construction->getCompletedAt()
            )
        )->toArray();

        $controlHubDto->canEnqueueNewBuilding = $planet->canEnqueueNewBuilding();
        $controlHubDto->canBuildSingleDrone = $this->droneService->canBuildDrone($planet);
        $controlHubDto->numberOfBuildableDrones = $this->droneService->getNumberOfBuildableDrones($planet);
        $controlHubDto->queuedDroneJobs = array_map(
            fn(DroneQueueJob $job) => new DroneQueueJobDTO(
                $job->getId(),
                $job->getDuration(),
                $job->getStartedAt(),
                $job->getCompletedAt(),
                $job->getCancelledAt(),
            ),
            $this->droneService->getDroneQueue($planet)->getJobs()
        );
        $controlHubDto->nextDroneCost = $this->droneService->getNextDroneCost($planet);
        $controlHubDto->nextDroneBuildTime = $this->droneService->getNextDroneBuildTime($planet);
        return $controlHubDto;
    }

    /**
     * Orchestrates the use case of Enqueuing a construction
     * - Validates user request
     * - Gets necessary configuration to calculate cost, time and requirements.
     * - Interacts with the Planet aggregate to enqueue
     */
    public function enqueueConstruction(int $planetId, string $buildingName): ControlHubDTO
    {
        $manager = $this->managerRegistry->getManager('world');
        $manager->clear();

        $manager->wrapInTransaction(function () use ($planetId, $buildingName, $manager) {
            $planet = $this->planetRepository->find($planetId);

            $buildingDefinition = $this->buildingRegistry->get($buildingName);

            $level = $planet->getNextLevelForBuilding($buildingDefinition);
            $cost = $this->costCalculator->getCostForObject($buildingDefinition, $level);
            $duration = $this->objectTimeService->getTimeForObject($planetId, $planet->getBuildingsAsGameObjects()->toArray(), $buildingDefinition->getAsGameObject(), $level, $cost);

            $planet->enqueueConstruction($buildingDefinition, $duration, $level, $cost);

            $manager->flush();

        });

        return $this->getControlHubOverview($planetId);
    }


    public function cancelConstruction(int $planetId, int $constructionId): ControlHubDTO
    {

        $manager = $this->managerRegistry->getManager('world');
        $manager->clear();
        $planet = $this->planetRepository->find($planetId);
        $manager->wrapInTransaction(function () use ($planet, $constructionId, $manager) {
            $planet->cancelConstruction($constructionId);
            $manager->flush();
        });

        return $this->getControlHubOverview($planetId);
    }

    public function terminateConstruction(int $planetId, int $constructionId): ControlHubDTO
    {
        $manager = $this->managerRegistry->getManager('world');
        $manager->clear();
        $planet = $this->planetRepository->find($planetId);
        if (!$this->security->isGranted('ROLE_CAN_TERMINATE_CONSTRUCTIONS')) {
            throw new AccessDeniedException("Access denied.");
        }

        $manager->wrapInTransaction(function () use ($planet, $constructionId, $manager) {
            $planet->terminateConstruction($constructionId);
            $manager->flush();
        });

        return $this->getControlHubOverview($planetId);
    }


    /**
     * Returns all possible constructions for this planet, excluding the ones for which requirements are not met
     * but including the ones for which the planet has no resources available
     * @return array<ConstructionDTO>
     */
    private function getPossibleConstructions(Planet $planet): array
    {

        $result = [];
        foreach ($this->buildingRegistry->getIterator() as $buildingDefinition) {
            if (!$planet->areBuildingRequirementsMet($buildingDefinition)) {
                continue;
            }
            $currentLevel = $planet->getBuildingLevel($buildingDefinition->getName());
            $nextLevel = $planet->getNextLevelForBuilding($buildingDefinition);
            $construction = new ConstructionDTO();
            $construction->buildingName = $buildingDefinition->getName();
            $construction->level = $nextLevel;
            $cost = $this->costCalculator->getCostForObject($buildingDefinition, $construction->level);
            $construction->isFullyBuilt = $nextLevel > $buildingDefinition->getMaxLevel();
            $construction->isFullyDemolished = $nextLevel < 0;

            // Do not expose cost and build time if requirements are not met.
            $construction->isCostSatisfied = $planet->hasStorageForPack($cost);

            $energyYield = $buildingDefinition->getEnergyConsumptionAtLevel($nextLevel);
            $currentEnergy = $buildingDefinition->getEnergyConsumptionAtLevel($currentLevel);

            $construction->energyYield = $energyYield;
            // Need to subtract current used energy to get a correct calculation
            $construction->isEnergyAvailable = $this->energyService->canYieldEnergy($energyYield - $currentEnergy, $planet);
            $construction->cost = $cost;
            $construction->buildTime = $this->objectTimeService->getTimeForObject($planet->getId(), $planet->getBuildingsAsGameObjects()->toArray(), $buildingDefinition->getAsGameObject(), $construction->level, $cost);

            $result[$buildingDefinition->getName()] = $construction;
        }

        return $result;
    }


}