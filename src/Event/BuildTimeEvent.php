<?php

namespace App\Event;

use App\Entity\World\Camp;
use App\ObjectDefinition\Building\BuildingDefinitionInterface;

class BuildTimeEvent extends GameEvent
{
    public function __construct(
        private readonly Camp                        $camp,
        private readonly BuildingDefinitionInterface $buildingConfigProvider,
        private readonly int                         $level,
        private int                                  $buildTime

    )
    {
    }

    public function getCamp(): Camp
    {
        return $this->camp;
    }

    public function getBuildingConfigProvider(): BuildingDefinitionInterface
    {
        return $this->buildingConfigProvider;
    }

    public function getLevel(): int
    {
        return $this->level;
    }


    public function getBuildTime(): int
    {
        return $this->buildTime;
    }

    public function setBuildTime(int $buildTime): BuildTimeEvent
    {
        $this->buildTime = $buildTime;
        return $this;
    }



}