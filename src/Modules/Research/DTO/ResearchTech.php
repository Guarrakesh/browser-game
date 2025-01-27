<?php

namespace App\Modules\Research\DTO;

use App\Model\ObjectRequirement;
use App\ObjectDefinition\Research\ResearchTechDefinitionInterface;

class ResearchTech
{
    public bool $satisfied = false;
    /** @var array<string,ObjectRequirement> */
    public readonly array $requirements;
    /**
     * @param ResearchTechDefinitionInterface $definition
     */
    public function __construct(public readonly ResearchTechDefinitionInterface $definition)
    {
        // TODO: Remove Dependnecy from ResearchTechDefinitionInterface which is a Application Layer object.
        $requirements = [];
        foreach ($this->definition->getRequires() as $require) {
            $req = new ObjectRequirement($require, 1);
            $requirements[$require] = $req;
        }

        $this->requirements = $requirements;

    }



}