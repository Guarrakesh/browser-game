<?php

namespace App\ObjectDefinition\Research;

use App\ObjectDefinition\AbstractDefinition;

class ResearchTechDefinition extends AbstractDefinition implements ResearchTechDefinitionInterface
{

    public function getRequires(): array
    {
        return $this->getConfig('requires');
    }
}