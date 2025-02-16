<?php

namespace App\Modules\Planet\Dto;

use App\Modules\Shared\Model\ResourcePack;

class PlanetDTO
{
    public int $id;
    public string $name;
    public int $playerId;
    public ResourcePack $storage;

    /** @var array<string, GameObjectLevel> */
    public array $buildings;

    public int $maxStorage = 0;

    public ResourcePack $hourlyProduction;

    public function __construct()
    {
    }


}