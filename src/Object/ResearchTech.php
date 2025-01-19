<?php

namespace App\Object;

use App\Entity\World\PlayerTech;
use App\Model\ObjectRequirement;
use App\ObjectDefinition\Research\ResearchTechDefinitionInterface;

class ResearchTech
{
    /** @var array<string,ObjectRequirement> */
    public readonly array $requirements;
    /**
     * @param ResearchTechDefinitionInterface $definition
     */
    public function __construct(public readonly ResearchTechDefinitionInterface $definition)
    {

        $requirements = [];
        foreach ($this->definition->getRequires() as $require) {
            $req = new ObjectRequirement($require, 1);
            $requirements[$require] = $req;
        }

        $this->requirements = $requirements;

    }

    public function isSatisfied(PlayerTech $tech): bool
    {
        foreach ($this->requirements as $requirement) {
            if (!$tech->hasLevel($requirement->getName(), $requirement->getLevel())) {
                return false;
            }
        }

        return true;
    }

}