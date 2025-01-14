<?php

namespace App\Event;

use App\Camp\Building\BuildingInterface;
use App\Entity\World\Camp;

class BuildTimeEvent extends GameEvent
{
    public function __construct(
        private readonly Camp              $camp,
        private readonly BuildingInterface $buildingConfigProvider,
        private readonly int      $level,
        private int               $buildTime

    )
    {
    }

    public function getCamp(): Camp
    {
        return $this->camp;
    }

    public function getBuildingConfigProvider(): BuildingInterface
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