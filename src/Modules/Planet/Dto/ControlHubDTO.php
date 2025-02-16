<?php

namespace App\Modules\Planet\Dto;

use App\Modules\Construction\DTO\ConstructionDTO;
use App\Modules\Construction\DTO\ConstructionQueueJobDTO;

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
}