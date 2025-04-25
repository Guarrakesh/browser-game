<?php

namespace App\Research\Model;

use App\Research\Dto\ObjectDefinition\ResearchTechDefinitionInterface;

interface TechDefinitionAwareInterface
{
    public function getTechName(): ?string;


    public function getDefinition(): ?ResearchTechDefinitionInterface;

    public function setDefinition(?ResearchTechDefinitionInterface $definition): TechDefinitionAwareInterface;
}