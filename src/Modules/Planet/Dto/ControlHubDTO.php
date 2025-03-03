<?php

namespace App\Modules\Planet\Dto;

use App\Modules\Shared\Dto\GameObjectLevel;
use App\Modules\Shared\Dto\GameObjectWithRequirements;

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