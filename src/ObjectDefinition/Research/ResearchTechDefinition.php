<?php

namespace App\ObjectDefinition\Research;

use App\Object\ResourcePack;
use App\ObjectDefinition\AbstractDefinition;
use App\ObjectDefinition\DefinitionWithCalculatorTrait;

class ResearchTechDefinition extends AbstractDefinition implements ResearchTechDefinitionInterface
{
    use DefinitionWithCalculatorTrait;

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