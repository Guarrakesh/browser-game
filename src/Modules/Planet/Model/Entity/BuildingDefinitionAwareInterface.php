<?php

namespace App\Modules\Planet\Model\Entity;

use App\Modules\Planet\GameObject\Building\BuildingDefinition;

interface BuildingDefinitionAwareInterface
{
    public function getBuildingName(): ?string;


    public function getDefinition(): ?BuildingDefinition;

    public function setDefinition(?BuildingDefinition $definition): BuildingDefinitionAwareInterface;
}