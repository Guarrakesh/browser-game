<?php

namespace App\Modules\Construction\DTO;

use App\Object\ResourcePack;

class ConstructionDTO
{
    public string $buildingName;

    public ResourcePack $cost;

    public int $level = 1;

    public int $buildTime = 0;


    public array $requirements = [];

    public bool $areRequirementsMet = false;
    public bool $isCostSatisfied = false;

    public bool $canBeBuilt = false;
}