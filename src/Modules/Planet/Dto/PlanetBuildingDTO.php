<?php

namespace App\Modules\Planet\Dto;

class PlanetBuildingDTO
{
    public string $name;
    public int $level;


    public function __construct(string $name, int $level)
    {
        $this->name = $name;
        $this->level = $level;
    }


}