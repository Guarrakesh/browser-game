<?php

namespace App\Modules\Construction\DTO;

use App\Modules\Core\DTO\PlanetDTO;

class PossibleConstructionsDTO
{

    /**
     * @param PlanetDTO $planet
     * @param array<string,ConstructionDTO> $constructions
     */
    public function __construct(public PlanetDTO $planet, public array $constructions)
    {

    }
}