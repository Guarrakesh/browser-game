<?php

namespace App\Modules\Core\DTO;

use App\Object\ResourcePack;

class PlanetDTO
{
    public int $id;
    public string $name;
    public ResourcePack $storage;

    /** @var array<string, PlanetBuildingDTO> */
    public array $buildings;

    public int $maxStorage = 0;

    public function __construct()
    {
    }


}