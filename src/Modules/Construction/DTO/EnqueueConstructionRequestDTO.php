<?php

namespace App\Modules\Construction\DTO;

use App\Modules\Core\DTO\PlanetDTO;
use Symfony\Component\Validator\Constraints\NotBlank;

class EnqueueConstructionRequestDTO
{

    #[NotBlank]
    public string $building;

    public int $planetId;
}