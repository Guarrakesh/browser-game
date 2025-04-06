<?php

namespace App\Modules\Planet\Dto;

use App\Modules\Shared\Model\ResourcePack;

class ConstructionDTO
{
    public string $buildingName;

    public ResourcePack $cost;

    public int $level = 1;

    public int $buildTime = 0;

    public bool $isCostSatisfied = false;

    public bool $isFullyBuilt = false;

    public bool $isFullyDemolished = false;
    public bool $isEnergyAvailable = false;
    public int $energyConsumption = 0;


}