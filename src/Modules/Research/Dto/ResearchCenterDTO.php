<?php

namespace App\Modules\Research\Dto;

use App\Modules\Shared\Dto\GameObjectWithRequirements;

class ResearchCenterDTO
{
    /**
     * @var array<GameObjectWithRequirements>
     */
    public array $techs = [];

    /**
     * @var array<ResearchQueueJobDTO>
     */
    public array $queuedJobs;

    /** @var array<string> */
    public array $playerTechs;

    /** @var array<string,ResearchTechDTO> */
    public array $possibleResearches;


}