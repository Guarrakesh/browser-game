<?php

namespace App\Planet\Dto;

use App\Shared\Dto\GameObjectLevel;
use App\Shared\Dto\GameObjectWithRequirements;
use App\Shared\Model\ResourcePack;

class ControlHubDTO
{


    /** @var array<GameObjectLevel> */
    public array $buildings = [];

    /** @var array<ConstructionQueueJobDTO> */
    public array $queuedJobs;

    /** @var array<ConstructionDTO> */
    public array $possibleConstructions;

    /** @var array<GameObjectWithRequirements> */
    public array $lockedConstructions;

    /** @var array<DroneQueueJobDTO> */
    public array $queuedDroneJobs;
    public bool $canEnqueueNewBuilding = false;

    public bool $canBuildSingleDrone = false;

    public int $numberOfBuildableDrones = 0;
    public ResourcePack $nextDroneCost;
    public int $nextDroneBuildTime;
}