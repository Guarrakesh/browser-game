<?php

namespace App\Shared\Dto;

use App\Planet\GameObject\Building\BuildingDefinitionInterface;
use App\Shared\GameObject\BaseDefinitionInterface;
use Symfony\Component\Serializer\Attribute\Ignore;

class GameObjectLevel
{

    public function __construct(

        private readonly GameObject              $object,
        private readonly int                     $level,
        private readonly BaseDefinitionInterface $definition,
    )
    {
    }

    #[Ignore]
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
            return $this->definition->getTotalEnergyAtLevel($this->level);
        }

        return 0;
    }

}