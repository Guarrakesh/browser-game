<?php

namespace App\Modules\Research\Model;

use App\Modules\Research\Dto\ObjectDefinition\ResearchTechDefinitionInterface;

interface TechDefinitionAwareInterface
{
    public function getTechName(): ?string;


    public function getDefinition(): ?ResearchTechDefinitionInterface;

    public function setDefinition(?ResearchTechDefinitionInterface $definition): TechDefinitionAwareInterface;
}