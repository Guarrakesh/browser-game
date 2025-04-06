<?php

namespace App\Modules\Shared\Dto;

use App\Modules\Planet\GameObject\Building\BuildingDefinitionInterface;
use App\Modules\Shared\GameObject\BaseDefinitionInterface;

class GameObjectLevel
{

    public function __construct(

        private readonly GameObject              $object,
        private readonly int                     $level,
        private readonly BaseDefinitionInterface $definition,
    )
    {
    }

    public function getDefinition(): BaseDefinitionInterface
    {
        return $this->definition;
    }


    public function getObject(): GameObject
    {
        return $this->object;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getEnergyConsumption(): int
    {
        if ($this->definition instanceof BuildingDefinitionInterface) {
            return $this->definition->getEnergyConsumptionAtLevel($this->level);
        }

        return 0;
    }

}