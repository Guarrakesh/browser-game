<?php

namespace App\Modules\Core\DTO;

class PlanetBuildingDTO
{
    public string $name;
    public int $level;
    public int $planetId;


    public function __construct(string $name, int $level, int $planetId)
    {
        $this->name = $name;
        $this->level = $level;
        $this->planetId = $planetId;
    }


}