<?php

namespace App\Modules\Construction\DTO;

use Symfony\Component\Validator\Constraints\NotBlank;

class EnqueueConstructionRequestDTO
{
    #[NotBlank]
    public string $building;

    #[NotBlank]
    public int $planetId;
}