<?php

namespace App\Modules\Planet\Dto;

use App\Modules\Construction\DTO\ConstructionDTO;
use App\Modules\Construction\DTO\ConstructionQueueJobDTO;
use App\Modules\Core\DTO\GameObjectDTO;

class ControlHubDTO
{

    /** @var array<GameObjectDTO> */
    public array $buildings = [];

    /** @var array<ConstructionQueueJobDTO> */
    public array $queuedJobs;

    /** @var array<ConstructionDTO> */
    public array $possibleConstructions;
}