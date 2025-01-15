<?php

namespace App\Event;

use App\Camp\Building\BuildingDefinitionInterface;
use App\Entity\World\Camp;
use App\Model\ResourcePack;

class BuildingCostEvent extends GameEvent
{
    public function __construct(
        private readonly Camp                        $camp,
        private readonly BuildingDefinitionInterface $buildingConfig,
        private readonly int                         $level,
        private ResourcePack                         $cost
    )
    {
    }

    public function getCamp(): Camp
    {
        return $this->camp;
    }


    public function getBuildingConfig(): BuildingDefinitionInterface
    {
        return $this->buildingConfig;
    }

    public function getCost(): ResourcePack
    {
        return $this->cost;
    }

    public function setCost(ResourcePack $cost): BuildingCostEvent
    {
        $this->cost = $cost;
        return $this;
    }

    public function getLevel(): int
    {
        return $this->level;
    }






}