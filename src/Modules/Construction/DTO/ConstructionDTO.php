<?php

namespace App\Modules\Construction\DTO;

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

}