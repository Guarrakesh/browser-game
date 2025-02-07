<?php

namespace App\Modules\Planet\Dto\ObjectDefinition\Research;

use App\Modules\Planet\Dto\ObjectDefinition\AbstractDefinition;
use App\Modules\Shared\Model\ObjectType;

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


    public function getType(): ObjectType
    {
        return ObjectType::ResearchTech;
    }


}