<?php

namespace App\Modules\Planet\Model\Entity;

use App\Modules\Planet\GameObject\Building\BuildingDefinitionInterface;

interface BuildingDefinitionAwareInterface
{
    public function getBuildingName(): ?string;


    public function getDefinition(): ?BuildingDefinitionInterface;

    public function setDefinition(?BuildingDefinitionInterface $definition): BuildingDefinitionAwareInterface;
}