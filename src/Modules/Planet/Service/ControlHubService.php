<?php

namespace App\Modules\Planet\Service;

use App\Modules\Construction\DTO\ConstructionDTO;
use App\Modules\Construction\DTO\ConstructionQueueJobDTO;
use App\Modules\Planet\Dto\ControlHubDTO;
use App\Modules\Planet\Dto\GameObjectWithRequirements;
use App\Modules\Planet\Dto\ObjectDefinition\Building\BuildingDefinition;
use App\Modules\Planet\Infra\Registry\BuildingRegistry;
use App\Modules\Planet\Infra\Repository\PlanetRepository;
use App\Modules\Planet\Model\DomainService\Cost\CostCalculator;
use App\Modules\Planet\Model\DomainService\ObjectTime\ObjectTimeService;
use App\Modules\Planet\Model\Entity\Planet;
use App\Modules\Planet\Model\Entity\PlanetConstruction;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ControlHubService
{

    public function __construct(
        private readonly CostCalculator    $costCalculator,
        private readonly BuildingRegistry  $buildingRegistry,
        private readonly PlanetRepository  $planetRepository,
        private readonly ObjectTimeService $objectTimeService,
        private readonly ManagerRegistry   $managerRegistry,
        private readonly Security          $security
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
        $manager->clear();;

        $manager->wrapInTransaction(function () use ($planetId, $buildingName, $manager) {
            $planet = $this->planetRepository->find($planetId);

            $buildingDefinition = $this->buildingRegistry->get($buildingName);
            // TODO: all this logic below should go into a DomainService ?

            $level = $planet->getNextLevelForBuilding($buildingDefinition);
            $cost = $this->costCalculator->getCostForObject($planet, $buildingDefinition, $level);
            $duration = $this->objectTimeService->getTimeForObject($planet, $buildingDefinition, $level, $cost);

            $planet->enqueueConstruction($buildingDefinition, $duration, $level, $cost);
            $planet->debitResources($cost);
            // Subtract cost


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
     * Returns all possible constructions for this planet, including the ones for which requirements are not met
     * or for which the planet has no resources available
     * @return array<ConstructionDTO>
     */
    private function getPossibleConstructions(Planet $planet): array
    {

        $result = [];
        foreach ($this->buildingRegistry->getIterator() as $buildingDefinition) {
            if (!$planet->areBuildingRequirementsMet($buildingDefinition)) {
                continue;
            }
            $nextLevel = $planet->getNextLevelForBuilding($buildingDefinition);
            $construction = new ConstructionDTO();
            $construction->buildingName = $buildingDefinition->getName();
            $construction->level = $nextLevel;
            $cost = $this->costCalculator->getCostForObject($planet, $buildingDefinition, $construction->level);
            $construction->isFullyBuilt = $nextLevel > $buildingDefinition->getMaxLevel();
            $construction->isFullyDemolished = $nextLevel < 0;

            // Do not expose cost and build time if requirements are not met.
            $construction->isCostSatisfied = $planet->hasStorageForPack($cost);
            $construction->cost = $cost;
            $construction->buildTime = $this->objectTimeService->getTimeForObject($planet, $buildingDefinition, $construction->level, $cost);

            $result[$buildingDefinition->getName()] = $construction;
        }

        return $result;
    }
}