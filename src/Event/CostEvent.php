<?php

namespace App\Event;

use App\Entity\World\Camp;
use App\Object\ResourcePack;
use App\ObjectDefinition\BaseDefinitionInterface;
use App\ObjectDefinition\Building\BuildingDefinitionInterface;

class CostEvent extends GameEvent
{
    public function __construct(
        private readonly Camp                    $camp,
        private readonly BaseDefinitionInterface $buildingConfig,
        private readonly int                     $level,
        private ResourcePack                     $cost,
        private readonly mixed                   $context
    )
    {

    }

    public function getCamp(): Camp
    {
        return $this->camp;
    }

    public function getBuildingConfig(): BaseDefinitionInterface
    {
        return $this->buildingConfig;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getCost(): ResourcePack
    {
        return $this->cost;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    public function setCost(ResourcePack $cost): CostEvent
    {
        $this->cost = $cost;
        return $this;
    }


}