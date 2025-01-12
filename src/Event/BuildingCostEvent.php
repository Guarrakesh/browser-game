<?php

namespace App\Event;

use App\Camp\Building\BuildingInterface;
use App\Entity\World\Camp;
use App\Model\ResourcePack;

class BuildingCostEvent extends GameEvent
{
    public function __construct(
        private readonly Camp     $camp,
        private readonly BuildingInterface $buildingConfig,
        private readonly int      $level,
        private ResourcePack      $cost
    )
    {
    }

    public function getCamp(): Camp
    {
        return $this->camp;
    }


    public function getBuildingConfig(): BuildingInterface
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