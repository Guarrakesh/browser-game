<?php

namespace App\Modules\Planet\Dto;

use App\Modules\Planet\Model\DroneQueue;
use App\Modules\Shared\Dto\GameObjectLevel;
use App\Modules\Shared\Dto\GameObjectWithRequirements;
use App\Modules\Shared\Model\ResourcePack;

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

    public DroneQueue $droneQueue;
    public bool $canEnqueueNewBuilding = false;

    public bool $canBuildSingleDrone = false;

    public int $numberOfBuildableDrones = 0;
    public ResourcePack $nextDroneCost;
    public int $nextDroneBuildTime;
}