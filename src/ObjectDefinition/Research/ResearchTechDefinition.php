<?php

namespace App\ObjectDefinition\Research;

use App\ObjectDefinition\AbstractDefinition;

class ResearchTechDefinition extends AbstractDefinition implements ResearchTechDefinitionInterface
{


    public function getRequires(): array
    {
        return $this->getConfig('requires');
    }

    public function getLabel(): string
    {
        return $this->getConfig('label');
    }

    public function getDescription(): string
    {
       return $this->getConfig('description');
    }


}