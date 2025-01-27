<?php

namespace App\Modules\Construction\DTO;

use App\Modules\Core\DTO\PlanetDTO;

class EnqueueConstructionRequestDTO
{

    public string $buildingName;
    public PlanetDTO $planet;
}