<?php

namespace App\Planet\Domain\Entity;

use App\Planet\GameObject\Building\BuildingDefinitionInterface;

interface BuildingDefinitionAwareInterface
{
    public function getBuildingName(): ?string;


    public function getDefinition(): ?BuildingDefinitionInterface;

    public function setDefinition(?BuildingDefinitionInterface $definition): BuildingDefinitionAwareInterface;
}