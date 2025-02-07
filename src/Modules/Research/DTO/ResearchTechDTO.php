<?php

namespace App\Modules\Research\DTO;

use App\Modules\Core\DTO\ObjectRequirementDTO;
use App\Modules\Planet\Dto\ObjectDefinition\Research\ResearchTechDefinitionInterface;

class ResearchTechDTO
{
    public bool $satisfied = false;
    /** @var array<string,ObjectRequirementDTO> */
    public readonly array $requirements;

    /**
     * @param ResearchTechDefinitionInterface $definition
     */
    public function __construct(public readonly ResearchTechDefinitionInterface $definition)
    {
        // TODO: Remove Dependnecy from ResearchTechDefinitionInterface which is a Application Layer object.
        $requirements = [];
        foreach ($this->definition->getRequires() as $require) {
            $req = new ObjectRequirementDTO($require, 1);
            $requirements[$require] = $req;
        }

        $this->requirements = $requirements;

    }



}