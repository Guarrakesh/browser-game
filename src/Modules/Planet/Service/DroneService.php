<?php

namespace App\Modules\Planet\Service;

use App\Exception\GameException;
use App\Modules\Planet\Dto\DroneAvailabiltyDTO;
use App\Modules\Planet\Infra\Repository\DroneAllocationRepository;
use App\Modules\Planet\Infra\Repository\DroneQueueRepository;
use App\Modules\Planet\Infra\Repository\PlanetRepository;
use App\Modules\Planet\Model\DroneQueue;
use App\Modules\Planet\Model\Entity\Drone\DroneQueueJob;
use App\Modules\Planet\Model\Entity\Planet;
use App\Modules\Shared\Dto\GameObject;
use App\Modules\Shared\Model\ObjectType;
use App\Modules\Shared\Model\ResourcePack;
use App\Modules\Shared\Service\ObjectTime\ObjectTimeService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class DroneService
{
    public function __construct(
        #[Autowire('@drone_configuration_service')] private DroneConfigurationService $droneConfigurationService,
        private DroneAllocationRepository                                             $droneAllocationRepository,
        private ManagerRegistry                                                       $managerRegistry,
        private EnergyService                                                         $powerService,
        private DroneQueueRepository                                                  $droneQueueRepository,
        private PlanetRepository                                                      $planetRepository, private ObjectTimeService $objectTimeService,

    )
    {
    }

    public function getNextDroneCost(Planet $planet): ResourcePack
    {
        return $this->droneConfigurationService->getCost($planet->getDronesCount());
    }

    public function getNextDroneBuildTime(Planet $planet): int
    {

        return $this->objectTimeService->getTimeForObject(
            $planet->getId(),
            $planet->getBuildingsAsGameObjects()->toArray(),
            new GameObject('drone', ObjectType::Drone),
            null,
            ResourcePack::fromIdentity(1)
        );
    }

    public function canBuildDrone(Planet $planet): bool
    {
        $dronePower = $this->droneConfigurationService->getEnergyConsumption();

        return $this->powerService->canYieldEnergy($dronePower, $planet)
            && $planet->hasResources($this->droneConfigurationService->getCost($planet->getDronesCount()));
    }

    public function getNumberOfBuildableDrones(Planet $planet): int
    {
        $dronePower = $this->droneConfigurationService->getEnergyConsumption();
        $availEnergy = $this->powerService->getAvailableEnergy($planet);

        return $availEnergy > 0 ? floor($availEnergy / $dronePower) : 0;
    }


    public function getDroneAvailability(Planet $planet): DroneAvailabiltyDTO
    {
        $totalDrones = $planet->getDronesCount();
        $droneAllocations = $this->droneAllocationRepository->findByPlanet($planet->getId());

        $used = 0;
        $allocations = [];

        foreach ($droneAllocations as $droneAllocation) {
            $used += $droneAllocation->getAmount();
            $allocations[] = $droneAllocation;
        }

        return new DroneAvailabiltyDTO($totalDrones, $used, $allocations);
    }

    public function getDroneQueue(Planet $planet): DroneQueue
    {
        return $this->droneQueueRepository->getDroneQueue($planet->getId());
    }

    public function enqueueDrone(int $planetId): void
    {
        $manager = $this->managerRegistry->getManager('world');
        $manager->clear();

        $manager->wrapInTransaction(function () use ($planetId, $manager) {
            $planet = $this->planetRepository->find($planetId);
            if (!$this->canBuildDrone($planet)) {
                throw new GameException("Can't build drones");
            }

            $droneQueue = $this->droneQueueRepository->getDroneQueue($planet->getId());

            $existingDrones = $planet->getDronesCount();
            $cost = $this->droneConfigurationService->getCost($existingDrones);
            $duration = $this->droneConfigurationService->getBuildTime($existingDrones);

            $job = new DroneQueueJob($planet->getId(), $cost->toArray());

            $droneQueue->enqueue($job, $duration);
            $planet->debitResources($cost);

            $manager->persist($job);
            $manager->flush();
        });

    }

}